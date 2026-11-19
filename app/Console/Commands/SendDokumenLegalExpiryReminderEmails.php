<?php

namespace App\Console\Commands;

use App\Mail\DokumenLegalExpiryReminderMail;
use App\Models\DokumenLegal;
use App\Models\DokumenLegalReminderLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendDokumenLegalExpiryReminderEmails extends Command
{
    protected $signature = 'dokumen:send-expiry-reminder-email
                            {--date= : Tanggal acuan format Y-m-d; default hari ini}
                            {--dry-run : Tampilkan penerima tanpa mengirim email}
                            {--only-email= : Batasi pada satu email penerima}
                            {--force : Kirim ulang walaupun log pengingat sudah ada}';

    protected $description = 'Kirim email pengingat dokumen legal pada H-7 dan hari jatuh tempo';

    public function handle(): int
    {
        try {
            $today = $this->option('date')
                ? Carbon::createFromFormat('Y-m-d', $this->option('date'))->startOfDay()
                : Carbon::today();
        } catch (Throwable) {
            $this->error('Nilai --date harus memakai format Y-m-d.');

            return self::INVALID;
        }

        $targetDates = [
            'h-0' => $today->toDateString(),
            'h-7' => $today->copy()->addDays(7)->toDateString(),
        ];

        $documents = DokumenLegal::query()
            ->with(['holding', 'kategori', 'site', 'penerbit'])
            ->where('status', 'aktif')
            ->whereNull('dokumen_pengganti')
            ->whereIn('tanggal_akhir', array_values($targetDates))
            ->orderBy('tanggal_akhir')
            ->orderBy('nama')
            ->get();

        if ($documents->isEmpty()) {
            $this->info('Tidak ada dokumen legal yang jatuh tempo H-7 atau H-0 pada '.$today->toDateString().'.');

            return self::SUCCESS;
        }

        $recipients = $this->recipients();

        if ($this->option('only-email')) {
            $recipients = $recipients->where('email', mb_strtolower(trim($this->option('only-email'))))->values();
        }

        if ($recipients->isEmpty()) {
            $this->warn('Tidak ditemukan penerima HRD/Superadmin dengan email valid.');

            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;
        $skipped = 0;
        $dryRunRows = [];

        foreach ($recipients as $recipient) {
            $pendingDocuments = $documents
                ->map(fn (DokumenLegal $document) => $this->documentReminderPayload($document, $targetDates))
                ->filter(function (array $payload) use ($recipient) {
                    if ($this->option('force')) {
                        return true;
                    }

                    return ! DokumenLegalReminderLog::query()
                        ->where('dokumen_id', $payload['id'])
                        ->where('recipient_email', $recipient['email'])
                        ->where('reminder_type', $payload['reminder_type'])
                        ->exists();
                })
                ->values();

            if ($pendingDocuments->isEmpty()) {
                $skipped++;
                continue;
            }

            if ($this->option('dry-run')) {
                foreach ($pendingDocuments as $document) {
                    $dryRunRows[] = [
                        $recipient['name'],
                        $recipient['email'],
                        $document['nomer_dukumen'],
                        $document['nama'],
                        $document['tanggal_akhir'],
                        $document['reminder_label'],
                    ];
                }
                continue;
            }

            try {
                Mail::mailer('hrd')->to($recipient['email'])->send(new DokumenLegalExpiryReminderMail(
                    $recipient['name'],
                    $pendingDocuments,
                    $this->actionUrl($pendingDocuments)
                ));

                foreach ($pendingDocuments as $document) {
                    DokumenLegalReminderLog::updateOrCreate(
                        [
                            'dokumen_id' => $document['id'],
                            'recipient_email' => $recipient['email'],
                            'reminder_type' => $document['reminder_type'],
                        ],
                        [
                            'recipient_user_id' => $recipient['user_id'],
                            'target_date' => $document['target_date'],
                            'sent_at' => now(),
                        ]
                    );
                }

                $sent++;
            } catch (Throwable $exception) {
                $failed++;
                Log::error('Gagal mengirim email pengingat dokumen legal.', [
                    'recipient_email' => $recipient['email'],
                    'exception' => $exception->getMessage(),
                ]);
                $this->error('Gagal mengirim ke '.$recipient['email'].': '.$exception->getMessage());
            }
        }

        if ($this->option('dry-run')) {
            $this->table(
                ['Penerima', 'Email', 'Nomor', 'Dokumen', 'Tanggal Akhir', 'Reminder'],
                $dryRunRows
            );
            $this->info('Dry run selesai: '.count($dryRunRows).' baris reminder.');

            return self::SUCCESS;
        }

        $this->info("Pengingat dokumen legal selesai: {$sent} email terkirim, {$skipped} penerima dilewati, {$failed} gagal.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function recipients(): Collection
    {
        return User::query()
            ->with(['Karyawan', 'KaryawanNIK'])
            ->whereHas('roleUsers.roleMenu', function ($query) {
                $query->whereIn('role_name', ['HRD', 'Superadmin']);
            })
            ->get()
            ->map(function (User $user) {
                $employee = $user->Karyawan ?? $user->KaryawanNIK;
                $email = mb_strtolower(trim((string) (
                    $employee?->email_kantor
                    ?: $employee?->email
                    ?: data_get($user, 'email')
                )));

                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return null;
                }

                return [
                    'user_id' => (string) $user->id,
                    'name' => $employee?->name ?: $user->username ?: 'User',
                    'email' => $email,
                ];
            })
            ->filter()
            ->unique('email')
            ->values();
    }

    private function documentReminderPayload(DokumenLegal $document, array $targetDates): array
    {
        $expiryDate = Carbon::parse($document->tanggal_akhir)->toDateString();
        $reminderType = $expiryDate === $targetDates['h-0'] ? 'h-0' : 'h-7';

        return [
            'id' => $document->id,
            'holding_code' => $document->holding?->holding_code,
            'nomer_dukumen' => $document->nomer_dukumen,
            'nama' => $document->nama,
            'kategori' => $document->kategori?->nama_kategori ?? '-',
            'tanggal_akhir' => Carbon::parse($document->tanggal_akhir)->format('d/m/Y'),
            'target_date' => $expiryDate,
            'reminder_type' => $reminderType,
            'reminder_label' => $reminderType === 'h-0' ? 'Jatuh tempo hari ini' : 'H-7',
        ];
    }

    private function actionUrl(Collection $documents): string
    {
        $holdingCode = $documents
            ->pluck('holding_code')
            ->filter()
            ->first();

        return $holdingCode
            ? route('dokumen_legal.dashboard', $holdingCode)
            : url('/dashboard/holding');
    }
}
