<?php

namespace App\Services;

use App\Models\ApprovalGroup;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\StrukturModel;
use Illuminate\Support\Collection;

class UnmappedShiftReminderRecipients
{
    private const SUPERVISOR_LEVEL = 4;

    public function loadApprovalGroups(): Collection
    {
        // Use the same menu as MappingShiftController::getApproval, without
        // matching unrelated menu IDs by substring (e.g. 34 and 134).
        $menuIds = Menu::query()
            ->where('url', 'mapping_shift/dashboard')
            ->pluck('id')
            ->map(fn ($id) => (string) $id);

        $groups = ApprovalGroup::query()->get()->filter(
            fn (ApprovalGroup $group) => collect($group->menu_id ?? [])
                ->map(fn ($id) => (string) $id)
                ->intersect($menuIds)->isNotEmpty()
        );

        return $groups->load([
            'approval.levelJabatan',
            'approval.ApprovalUser',
        ]);
    }

    public function loadStructure(): Collection
    {
        return StrukturModel::query()->with([
            'Jabatan.LevelJabatan',
            'UserKaryawan.Karyawan' => fn ($query) => $query
                ->select('id', 'name', 'email', 'status_aktif'),
        ])->get()->keyBy('id');
    }

    /** @return array{recipients: Collection, reasons: Collection} */
    public function resolve(Karyawan $employee, Collection $approvalGroups, Collection $structure): array
    {
        $position = $employee->KaryawanJabatanOne;
        $employeeLevel = $position?->Jabatan?->LevelJabatan?->level_jabatan;
        $candidates = collect();
        $reasons = collect();

        if (! $position || ! is_numeric($employeeLevel) || ! $position->departemen_id) {
            return ['recipients' => $candidates, 'reasons' => collect(['Data jabatan, level, atau departemen karyawan belum lengkap.'])];
        }

        if ((int) $employeeLevel <= self::SUPERVISOR_LEVEL) {
            return ['recipients' => $candidates, 'reasons' => collect(['Karyawan berada pada level supervisor atau lebih tinggi; tidak dialihkan ke rekan supervisor atau manager.'])];
        }

        $authorizedIds = collect();
        foreach ($approvalGroups as $group) {
            if ($group->holding_id !== $position->holding_id) {
                continue;
            }

            foreach ($group->approval as $approval) {
                $approvalLevel = $approval->levelJabatan?->level_jabatan;

                if ($approval->holding_id !== $position->holding_id
                    || ! in_array($position->departemen_id, $approval->departemen_id ?? [], true)
                    || ! is_numeric($approvalLevel)
                    || (int) $approvalLevel !== self::SUPERVISOR_LEVEL) {
                    continue;
                }

                $authorizedIds = $authorizedIds->merge($approval->ApprovalUser->pluck('karyawan_id'));
            }
        }

        if ($authorizedIds->isEmpty()) {
            $reasons->push('Belum ada penanggung jawab level 4 pada approval Mapping Shift untuk holding dan departemen karyawan.');
        }

        $employeeNodes = $structure->where('holding', $position->holding_id)
            ->where('jabatan', $position->jabatan_id);

        if ($employeeNodes->isEmpty()) {
            $reasons->push('Jabatan karyawan belum ditemukan pada struktur organisasi holding ini.');
        }

        foreach ($employeeNodes as $node) {
            $visited = [$node->id => true];
            $current = $node;
            $foundSupervisor = false;

            while ($current->parent) {
                $parent = $structure->get($current->parent);

                if (! $parent || isset($visited[$parent->id]) || $parent->holding !== $position->holding_id) {
                    $reasons->push('Jalur struktur organisasi terputus, berulang, atau melintasi holding.');
                    break;
                }

                $visited[$parent->id] = true;
                $level = $parent->Jabatan?->LevelJabatan?->level_jabatan;
                if (! is_numeric($level)) {
                    $reasons->push('Level jabatan atasan pada struktur organisasi belum lengkap.');
                    break;
                }

                if ((int) $level < self::SUPERVISOR_LEVEL) {
                    break;
                }

                if ((int) $level === self::SUPERVISOR_LEVEL) {
                    $foundSupervisor = true;
                    // UserKaryawan matches jabatan_id only. Enforce the holding
                    // here to prevent recipients from another company.
                    $supervisors = $parent->UserKaryawan->filter(fn ($assignment) => (int) $assignment->is_active === 1
                        && $assignment->holding_id === $position->holding_id
                        && $assignment->jabatan_id === $parent->jabatan
                    )->pluck('Karyawan')->filter(fn ($person) => $person && $person->status_aktif === 'AKTIF' && $person->id !== $employee->id
                    )->unique('id');

                    if ($supervisors->isEmpty()) {
                        $reasons->push('Jabatan supervisor level 4 pada jalur struktur belum memiliki karyawan aktif.');
                    }

                    foreach ($supervisors as $supervisor) {
                        if (! $authorizedIds->contains($supervisor->id)) {
                            $reasons->push($supervisor->name.': belum terdaftar pada approval Mapping Shift level 4 untuk holding dan departemen karyawan.');
                        } elseif (! filter_var($supervisor->email, FILTER_VALIDATE_EMAIL)) {
                            $reasons->push($supervisor->name.': email kosong atau tidak valid.');
                        } else {
                            $candidates->push($supervisor);
                        }
                    }

                    // Stop at the responsible supervisor even when unavailable
                    // or unauthorized; never substitute another supervisor/manager.
                    break;
                }

                $current = $parent;
            }

            if (! $foundSupervisor) {
                $reasons->push('Tidak ditemukan supervisor level 4 pada jalur atasan karyawan.');
            }
        }

        return ['recipients' => $candidates->unique('id')->values(), 'reasons' => $reasons->unique()->values()];
    }
}
