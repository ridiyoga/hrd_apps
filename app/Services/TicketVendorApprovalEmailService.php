<?php

namespace App\Services;

use App\Mail\TicketVendorApprovalNotificationMail;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\TicketApprovalValidator;
use App\Models\TicketVendorHead;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class TicketVendorApprovalEmailService
{
    /**
     * Vendor step order & column mapping (mirrors $vendorSteps in ApprovalController).
     */
    private array $vendorSteps = [
        'atasan1'   => ['id' => 'vd_approve1',  'name' => 'vd_approve1_name',  'label' => 'Diketahui Oleh'],
        'atasan2'   => ['id' => 'vd_approve2',  'name' => 'vd_approve2_name',  'label' => 'Disetujui Oleh'],
        'checker'   => ['id' => 'vd_checker',   'name' => 'vd_checker_name',   'label' => 'Diperiksa Oleh'],
        'validator' => ['id' => 'vd_validator', 'name' => 'vd_validator_name', 'label' => 'Divalidasi Oleh'],
    ];

    /**
     * Send an email notification to the next approver after a step has been approved.
     *
     * @param  TicketVendorHead  $vendor  The vendor head (already saved with new current_step).
     * @param  string|null       $menuId  The approval menu ID for building the action URL.
     * @param  string            $trigger 'submitted' | 'approved'
     */
    public function sendNextActionNotification(
        TicketVendorHead $vendor,
        ?string $menuId = null,
        string $trigger = 'approved'
    ): void {
        $vendor->refresh();

        // Nothing to do if vendor is fully approved or rejected
        if (!in_array($vendor->status, ['process'], true)) {
            return;
        }

        $recipients = $this->resolveNextRecipients($vendor);
        if ($recipients->isEmpty()) {
            return;
        }

        $recipients
            ->unique(fn(array $r) => strtolower($r['email']))
            ->each(function (array $recipient) use ($vendor, $menuId, $trigger) {
                try {
                    $actionUrl = $this->buildActionUrl($vendor, $menuId, $recipient['role']);
                    Mail::mailer('hrd')->to($recipient['email'])->send(
                        new TicketVendorApprovalNotificationMail(
                            $vendor,
                            $recipient['name'],
                            $recipient['label'],
                            $actionUrl,
                            $trigger
                        )
                    );
                } catch (\Throwable $exception) {
                    Log::error('Gagal mengirim email approval vendor.', [
                        'vendor_id'        => $vendor->id,
                        'nomor_pengajuan'  => $vendor->nomor_pengajuan_fis,
                        'recipient_email'  => $recipient['email'],
                        'recipient_role'   => $recipient['role'],
                        'exception'        => $exception->getMessage(),
                    ]);
                }
            });
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve the next recipient(s) based on vendor's current_step.
     */
    private function resolveNextRecipients(TicketVendorHead $vendor): Collection
    {
        $currentStep = $vendor->current_step;

        // If current_step is null or 'done', no more approvers
        if (!$currentStep || $currentStep === 'done') {
            return collect();
        }

        if (!isset($this->vendorSteps[$currentStep])) {
            return collect();
        }

        $cols    = $this->vendorSteps[$currentStep];
        $subjectId = (string) ($vendor->{$cols['id']} ?? '');

        if (!$subjectId) {
            return collect();
        }

        // For 'validator' step: look up via TicketApprovalValidator if configured
        if ($currentStep === 'validator') {
            return $this->collectValidatorRecipients($vendor);
        }

        return $this->collectSingleRecipient($subjectId, $currentStep, $cols['label']);
    }

    private function collectSingleRecipient(string $subjectId, string $role, string $label): Collection
    {
        $karyawan  = $this->resolveKaryawanBySubject($subjectId);
        $recipient = $this->recipientFromKaryawan($karyawan, $role, $label);
        return collect($recipient ? [$recipient] : []);
    }

    private function collectValidatorRecipients(TicketVendorHead $vendor): Collection
    {
        $environment = match ((int) $vendor->system_type) {
            1 => 'epicor',
            2, 3 => 'accurate',
            default => null,
        };

        $systemType = (string) $vendor->system_type;

        return TicketApprovalValidator::with('karyawan')
            ->where('is_active', true)
            ->where(function ($query) use ($environment, $systemType) {
                $query->whereNull('environment');
                if (!is_null($environment)) {
                    $query->orWhere('environment', $environment)
                          ->orWhere('environment', $systemType);
                }
            })
            ->get()
            ->map(fn(TicketApprovalValidator $v) => $this->recipientFromKaryawan(
                $v->karyawan,
                'validator',
                'Divalidasi Oleh'
            ))
            ->filter()
            ->values();
    }

    private function recipientFromKaryawan(?Karyawan $karyawan, string $role, string $label): ?array
    {
        if (!$karyawan) {
            return null;
        }

        // Resolusi delegasi jika karyawan sedang cuti / sakit
        $delegasi = app(KaryawanPenggantiService::class)->cariKaryawanPenggantiAktif($karyawan);
        $karyawanAktif = $delegasi['karyawan'] ?: $karyawan;

        $email = trim((string) ($karyawanAktif->email_kantor ?? $karyawanAktif->email ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $resolvedLabel = $label;
        if ($delegasi['is_pengganti'] && $delegasi['karyawan_asli']) {
            $resolvedLabel = "{$label} (Pengganti an. {$delegasi['karyawan_asli']->name})";
        }

        return [
            'email' => $email,
            'name'  => $karyawanAktif->name ?? '-',
            'role'  => $role,
            'label' => $resolvedLabel,
        ];
    }

    private function resolveKaryawanBySubject(string $subjectId): ?Karyawan
    {
        $karyawan = $this->findKaryawanByIdentifier($subjectId);
        if ($karyawan) {
            return $karyawan;
        }
        // Fallback: maybe stored as user id
        $user = User::find($subjectId);
        if (!$user || !$user->karyawan_id) {
            return null;
        }
        return $this->findKaryawanByIdentifier((string) $user->karyawan_id);
    }

    private function findKaryawanByIdentifier(string $identifier): ?Karyawan
    {
        return Karyawan::where(function ($query) use ($identifier) {
            if (Uuid::isValid($identifier)) {
                $query->where('id', $identifier)
                    ->orWhere('nomor_identitas_karyawan', $identifier);
                return;
            }
            $query->where('nomor_identitas_karyawan', $identifier);
        })->first();
    }

    private function buildActionUrl(TicketVendorHead $vendor, ?string $menuId, string $role): string
    {
        $resolvedMenuId = $menuId ?: $this->resolveVendorMenuId();
        return route('approval.ticket.vendor.show', [$resolvedMenuId, $vendor->id]);
    }

    private function resolveVendorMenuId(): int
    {
        return (int) (
            Menu::where('url', 'like', '%approval/ticket/vendor%')->value('id')
            ?? 0
        );
    }
}
