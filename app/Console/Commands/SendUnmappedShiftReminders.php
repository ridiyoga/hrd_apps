<?php

namespace App\Console\Commands;

use App\Mail\UnmappedShiftReminderMail;
use App\Models\Calendar;
use App\Models\Karyawan;
use App\Services\UnmappedShiftReminderRecipients;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendUnmappedShiftReminders extends Command
{
    protected $signature = 'shift:remind-unmapped
                            {--date= : Tanggal acuan dalam format Y-m-d; default hari ini}
                            {--dry-run : Tampilkan penerima tanpa mengirim email}
                            {--only-email= : Batasi pada satu email atasan untuk pengecekan}
                            {--force : Kirim ulang walaupun pengingat hari ini sudah pernah dikirim}';

    protected $description = 'Kirim pengingat shift ke supervisor level 4 pada jalur struktur yang terdaftar di approval Mapping Shift';

    public function handle(UnmappedShiftReminderRecipients $recipientResolver): int
    {
        try {
            $referenceDate = $this->option('date')
                ? Carbon::createFromFormat('Y-m-d', $this->option('date'))->startOfDay()
                : Carbon::today();
        } catch (Throwable) {
            $this->error('Nilai --date harus memakai format Y-m-d.');

            return self::INVALID;
        }

        // Hanya periksa tujuh hari yang sudah selesai agar karyawan tidak ditandai
        // belum mapping ketika hari berjalan masih sedang diproses oleh atasannya.
        $periodEnd = $referenceDate->copy()->subDay();
        $periodStart = $periodEnd->copy()->subDays(6);
        $dates = collect(range(0, 6))
            ->map(fn (int $offset) => $periodStart->copy()->addDays($offset)->toDateString());

        $holidayDates = Calendar::query()
            ->where('status', 1)
            ->whereBetween('start_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->pluck('start_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $employees = Karyawan::query()
            ->with([
                'KontrakKerja:id,holding_code,holding_name',
                'KaryawanJabatanOne' => fn ($query) => $query
                    ->where('main', 1)
                    ->where('is_active', 1),
                'KaryawanJabatanOne.Jabatan:id,nama_jabatan,level_id',
                'KaryawanJabatanOne.Jabatan.LevelJabatan',
                'KaryawanJabatanOne.Departemen:id,nama_departemen',
                'KaryawanJabatanOne.Divisi:id,nama_divisi',
                'MappingShift' => fn ($query) => $query
                    ->whereNotNull('is_active')
                    ->where('status_approved', 1)
                    ->whereBetween('tanggal_masuk', [$periodStart->toDateString(), $periodEnd->toDateString()])
                    ->select('id', 'karyawan_id', 'tanggal_masuk'),
                'CutiDetail' => fn ($query) => $query
                    ->where('status', 1)
                    ->whereBetween('tgl_cuti', [$periodStart->toDateString(), $periodEnd->toDateString()])
                    ->select('id', 'id_karyawan', 'tgl_cuti'),
            ])
            ->where('kategori', 'Karyawan Bulanan')
            ->where('status_aktif', 'AKTIF')
            ->where(function ($query) {
                $query->whereNull('shift')->orWhere('shift', '!=', 'NON SHIFT');
            })
            ->whereHas('KaryawanJabatanOne', fn ($query) => $query
                ->where('main', 1)
                ->where('is_active', 1))
            ->get();

        $problems = $employees->map(function (Karyawan $employee) use ($dates, $holidayDates) {
            $mappedDates = $employee->MappingShift
                ->pluck('tanggal_masuk')
                ->map(fn ($date) => Carbon::parse($date)->toDateString())
                ->flip();
            $leaveDates = $employee->CutiDetail
                ->pluck('tgl_cuti')
                ->map(fn ($date) => Carbon::parse($date)->toDateString())
                ->flip();

            $missingDates = $dates->reject(fn (string $date) => $holidayDates->has($date)
                || $mappedDates->has($date)
                || $leaveDates->has($date)
            )->values();

            if ($missingDates->isEmpty()) {
                return null;
            }

            $position = $employee->KaryawanJabatanOne;

            return [
                'employee' => $employee,
                'employee_position_id' => $position->jabatan_id,
                'holding_id' => $position->holding_id,
                'missing_dates' => $missingDates,
                'department' => $position->Departemen->nama_departemen ?? '-',
                'division' => $position->Divisi->nama_divisi ?? '-',
                'position' => $position->Jabatan->nama_jabatan ?? '-',
                'mapping_url' => url('karyawan/mapping_shift/'.($employee->KontrakKerja->holding_code ?? '')),
            ];
        })->filter()->values();

        if ($problems->isEmpty()) {
            $this->info('Tidak ada karyawan tanpa mapping shift pada periode '.$periodStart->toDateString().' s.d. '.$periodEnd->toDateString().'.');

            return self::SUCCESS;
        }

        $approvalGroups = $recipientResolver->loadApprovalGroups();
        $structure = $recipientResolver->loadStructure();
        $recipients = collect();
        $undeliverable = collect();

        foreach ($problems as $problem) {
            $resolution = $recipientResolver->resolve($problem['employee'], $approvalGroups, $structure);
            $supervisors = $resolution['recipients'];

            if ($supervisors->isEmpty()) {
                $problem['recipient_reason'] = $resolution['reasons']->join(' ');
                $undeliverable->push($problem);
                Log::warning('Pengingat mapping shift tidak memiliki penerima supervisor yang memenuhi syarat.', [
                    'karyawan_id' => $problem['employee']->id,
                    'nama_karyawan' => $problem['employee']->name,
                    'jabatan_karyawan_id' => $problem['employee_position_id'],
                    'holding_id' => $problem['holding_id'],
                    'alasan' => $problem['recipient_reason'],
                ]);

                continue;
            }

            foreach ($supervisors as $supervisor) {
                $emailKey = mb_strtolower(trim($supervisor->email));

                if (! $recipients->has($emailKey)) {
                    $recipients->put($emailKey, [
                        'supervisor' => $supervisor,
                        'employees' => collect(),
                    ]);
                }

                $recipient = $recipients->get($emailKey);
                $recipient['employees']->put($problem['employee']->id, $problem);
                $recipients->put($emailKey, $recipient);
            }
        }

        if ($this->option('only-email')) {
            $onlyEmail = mb_strtolower(trim($this->option('only-email')));
            $recipients = $recipients->only($onlyEmail);
        }

        if ($recipients->isEmpty()) {
            $this->warn('Tidak ditemukan penerima yang sesuai dengan filter, struktur organisasi, dan approval Mapping Shift.');

            if (! $this->option('dry-run')) {
                return self::SUCCESS;
            }
        }

        $sent = 0;
        $failed = 0;
        $cache = Cache::store('file');

        foreach ($recipients as $email => $recipient) {
            $employeeList = $recipient['employees']->values();

            if ($this->option('dry-run')) {
                foreach ($employeeList as $item) {
                    $dryRunRows[] = [
                        $recipient['supervisor']->name,
                        $email,
                        $item['employee']->name,
                        $item['employee']->nomor_identitas_karyawan,
                        $item['missing_dates']->map(
                            fn (string $date) => Carbon::parse($date)->format('d/m/Y')
                        )->join(', '),
                    ];
                }

                continue;
            }

            $cacheKey = 'unmapped-shift-reminder:'.$referenceDate->toDateString().':'.sha1($email);

            if (! $this->option('force') && ! $cache->add($cacheKey, 'sending', now()->addMinutes(10))) {
                $this->line('Lewati '.$email.': pengingat hari ini sudah dikirim atau sedang diproses.');

                continue;
            }

            try {
                Mail::mailer('hrd')->to($email)->send(new UnmappedShiftReminderMail(
                    $recipient['supervisor']->name,
                    $employeeList,
                    $periodStart,
                    $periodEnd,
                ));

                $cache->put($cacheKey, 'sent', now()->addDays(2));
                $sent++;
            } catch (Throwable $exception) {
                $cache->forget($cacheKey);
                $failed++;
                Log::error('Gagal mengirim pengingat mapping shift.', [
                    'email' => $email,
                    'exception' => $exception->getMessage(),
                ]);
                $this->error('Gagal mengirim ke '.$email.': '.$exception->getMessage());
            }
        }

        if ($this->option('dry-run')) {
            $this->table(
                ['Atasan', 'Email', 'Karyawan', 'NIK', 'Tanggal belum mapping'],
                $dryRunRows ?? []
            );

            if ($undeliverable->isNotEmpty()) {
                $this->newLine();
                $this->warn('Karyawan berikut tidak memiliki penerima supervisor level 4 yang memenuhi syarat:');
                $this->table(
                    ['Karyawan', 'NIK', 'Jabatan', 'Alasan tidak terkirim', 'Tanggal belum mapping'],
                    $undeliverable->map(fn (array $item) => [
                        $item['employee']->name,
                        $item['employee']->nomor_identitas_karyawan,
                        $item['position'],
                        wordwrap($item['recipient_reason'], 65, "\n"),
                        $item['missing_dates']->map(
                            fn (string $date) => Carbon::parse($date)->format('d/m/Y')
                        )->join(', '),
                    ])->all()
                );
            }

            $coveredEmployees = $recipients
                ->flatMap(fn (array $recipient) => $recipient['employees']->keys())
                ->unique()
                ->count();

            $this->info(
                'Dry run selesai: '.$problems->count().' karyawan bermasalah; '
                .$coveredEmployees.' tercakup dalam '.$recipients->count().' email atasan; '
                .$undeliverable->count().' tanpa penerima.'
            );

            return self::SUCCESS;
        }

        $this->info("Pengingat mapping shift selesai: {$sent} terkirim, {$failed} gagal.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
