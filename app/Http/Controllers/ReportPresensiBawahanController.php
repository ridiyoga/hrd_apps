<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Calendar;
use App\Models\CutiDetail;
use App\Models\Holding;
use App\Models\IzinLibur;
use App\Models\IzinSakitDetail;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\MappingShift;
use App\Models\Menu;
use App\Models\PresensiBawahanRelation;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\StrukturModel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class ReportPresensiBawahanController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }

        $holdingData = Holding::where('holding_code', $holding)->firstOrFail();

        return view('admin.report.presensi_bawahan.index', [
            'holding' => $holdingData,
            'menus' => $this->getSidebarMenus(),
        ]);
    }

    public function employees($holding)
    {
        try {
            Holding::where('holding_code', $holding)->firstOrFail();
            $assignments = $this->allEmployeeAssignments();
            $items = $assignments
                ->sortBy(fn(KaryawanJabatan $assignment) => $assignment->Karyawan->name ?? '')
                ->values()
                ->map(fn(KaryawanJabatan $assignment) => $this->employeePayload($assignment));

            return response()->json([
                'code' => 200,
                'summary' => [
                    'total_karyawan' => $items->count(),
                ],
                'data' => $items,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function scope($holding, $karyawanId)
    {
        try {
            $holdingData = Holding::where('holding_code', $holding)->firstOrFail();
            $assignments = $this->allEmployeeAssignments();
            $selectedAssignment = $assignments
                ->where('holding_id', $holdingData->id)
                ->firstWhere('karyawan_id', $karyawanId)
                ?: $assignments->firstWhere('karyawan_id', $karyawanId);

            if (!$selectedAssignment || !$selectedAssignment->Karyawan) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Karyawan tidak ditemukan pada holding ini.',
                ], 404);
            }

            $scopeIds = $this->savedDownlineIds($holdingData->id, $karyawanId)->prepend($karyawanId)->unique()->values();
            $scopeEmployees = $assignments
                ->whereIn('karyawan_id', $scopeIds)
                ->sortBy(fn(KaryawanJabatan $assignment) => $assignment->Karyawan->name ?? '')
                ->values()
                ->map(fn(KaryawanJabatan $assignment) => $this->employeePayload($assignment));

            $uplines = $this->uplines($assignments, $selectedAssignment, $holdingData->id);
            $directDownlines = $this->directDownlines($assignments, $selectedAssignment, $holdingData->id);
            $structureDirectDownlines = $this->structureDirectDownlines($assignments, $selectedAssignment, $holdingData->id);

            return response()->json([
                'code' => 200,
                'data' => [
                    'employee' => $this->employeePayload($selectedAssignment),
                    'uplines' => $uplines,
                    'direct_downlines' => $directDownlines,
                    'structure_direct_downlines' => $structureDirectDownlines,
                    'scope_employees' => $scopeEmployees,
                    'summary' => [
                        'total_scope' => $scopeEmployees->count(),
                        'total_downline' => max($scopeEmployees->count() - 1, 0),
                        'total_direct_downline' => $directDownlines->count(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function saveRelations($holding, Request $request)
    {
        try {
            $holdingData = Holding::where('holding_code', $holding)->firstOrFail();
            $atasanId = $request->input('atasan_id');
            $bawahanIds = collect($request->input('bawahan_ids', []))
                ->filter()
                ->unique()
                ->reject(fn($id) => (string) $id === (string) $atasanId)
                ->values();

            $employeeIds = $this->allEmployeeAssignments()->pluck('karyawan_id')->map(fn($id) => (string) $id);
            if (!$atasanId || !$employeeIds->contains((string) $atasanId) || $bawahanIds->diff($employeeIds)->isNotEmpty()) {
                return response()->json(['code' => 422, 'message' => 'Atasan atau bawahan tidak valid pada holding ini.'], 422);
            }

            DB::transaction(function () use ($holdingData, $atasanId, $bawahanIds) {
                PresensiBawahanRelation::where('holding_id', $holdingData->id)
                    ->where('atasan_id', $atasanId)
                    ->delete();

                foreach ($bawahanIds as $bawahanId) {
                    PresensiBawahanRelation::create([
                        'holding_id' => $holdingData->id,
                        'atasan_id' => $atasanId,
                        'bawahan_id' => $bawahanId,
                        'created_by' => Auth::id(),
                    ]);
                }
            });

            return response()->json(['code' => 200, 'message' => 'Relasi presensi bawahan berhasil disimpan.']);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function attendance($holding, $karyawanId, Request $request)
    {
        try {
            $holdingData = Holding::where('holding_code', $holding)->firstOrFail();
            $start = Carbon::parse($request->start_date ?: now()->toDateString())->startOfDay();
            $end = Carbon::parse($request->end_date ?: $start->toDateString())->endOfDay();

            if ($start->diffInDays($end) > 31) {
                return response()->json([
                    'code' => 422,
                    'message' => 'Rentang tanggal maksimal 31 hari.',
                ], 422);
            }

            $assignments = $this->allEmployeeAssignments();
            $selectedAssignment = $assignments
                ->where('holding_id', $holdingData->id)
                ->firstWhere('karyawan_id', $karyawanId)
                ?: $assignments->firstWhere('karyawan_id', $karyawanId);

            if (!$selectedAssignment || !$selectedAssignment->Karyawan) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Karyawan tidak ditemukan pada holding ini.',
                ], 404);
            }

            $scopeIds = $this->downlineIds($assignments, $selectedAssignment, $holdingData->id)
                ->prepend($karyawanId)
                ->unique()
                ->values();

            $employees = Karyawan::with(['Absensi' => function ($query) use ($start, $end) {
                $query->whereBetween('LogTime', [$start, $end])
                    ->orderBy('LogTime', 'ASC');
            }])
                ->whereIn('id', $scopeIds)
                ->where('status_aktif', 'AKTIF')
                ->get()
                ->keyBy('id');

            $assignmentMap = $assignments->keyBy('karyawan_id');
            $mappingShifts = MappingShift::with('Shift')
                ->whereIn('karyawan_id', $scopeIds)
                ->whereBetween('tanggal_masuk', [$start->toDateString(), $end->toDateString()])
                ->whereNotNull('is_active')
                ->get()
                ->groupBy('karyawan_id');

            $nonShift = Shift::where('nama_shift', 'NON SHIFT')->first();
            $period = CarbonPeriod::create($start->toDateString(), $end->toDateString());
            $rows = collect();

            foreach ($employees->sortBy('name') as $employee) {
                foreach ($period as $date) {
                    $rows->push($this->attendanceRow(
                        $employee,
                        $assignmentMap->get($employee->id),
                        $date->copy(),
                        $mappingShifts->get($employee->id, collect()),
                        $nonShift
                    ));
                }
            }

            return response()->json([
                'code' => 200,
                'summary' => [
                    'total_karyawan' => $employees->count(),
                    'total_rows' => $rows->count(),
                    'hadir' => $rows->where('status_group', 'hadir')->count(),
                    'tidak_hadir' => $rows->where('status_group', 'tidak_hadir')->count(),
                    'belum_dijadwalkan' => $rows->where('status_group', 'belum_dijadwalkan')->count(),
                    'izin' => $rows->where('status_group', 'izin')->count(),
                    'libur' => $rows->where('status_group', 'libur')->count(),
                ],
                'data' => $rows->values(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function exportMobile($holdingId, Request $request)
    {
        try {
            $holdingData = Holding::findOrFail($holdingId);
            $karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            if (!$karyawan) {
                return response()->json(['message' => 'Akun Anda belum terhubung dengan data karyawan. Hubungi admin.'], 422);
            }
            $karyawanId = $karyawan->id;
            $start = Carbon::parse($request->start_date ?: now()->startOfMonth()->toDateString())->startOfDay();
            $end = Carbon::parse($request->end_date ?: now()->toDateString())->endOfDay();

            if ($start->gt($end) || $start->diffInDays($end) > 31) {
                return response()->json(['message' => 'Rentang tanggal maksimal 31 hari dan tanggal harus valid.'], 422);
            }

            $assignments = $this->employeeAssignments($holdingData->id);
            $selectedAssignment = $assignments->firstWhere('karyawan_id', $karyawanId);
            if (!$selectedAssignment) {
                return response()->json(['message' => 'Anda belum terdaftar pada holding yang dipilih. Periksa penempatan holding di data karyawan.'], 422);
            }

            $scopeIds = $this->savedDownlineIds($holdingData->id, $karyawanId);
            if ($scopeIds->isEmpty()) {
                $scopeIds = $this->downlineIds($assignments, $selectedAssignment, $holdingData->id);
            }
            if ($scopeIds->isEmpty()) {
                return response()->json(['message' => 'Belum ada bawahan untuk Anda pada holding ini. Admin perlu menyusun dan menyimpan relasi bawahan terlebih dahulu.'], 422);
            }
            $employees = Karyawan::with(['Absensi' => function ($query) use ($start, $end) {
                $query->whereBetween('LogTime', [$start, $end])->orderBy('LogTime');
            }])->whereIn('id', $scopeIds)->where('status_aktif', 'AKTIF')->get()->keyBy('id');
            $assignmentMap = $assignments->keyBy('karyawan_id');
            $mappingShifts = MappingShift::with('Shift')
                ->whereIn('karyawan_id', $scopeIds)
                ->whereBetween('tanggal_masuk', [$start->toDateString(), $end->toDateString()])
                ->whereNotNull('is_active')
                ->get()->groupBy('karyawan_id');
            $period = CarbonPeriod::create($start->toDateString(), $end->toDateString());
            $nonShift = Shift::where('nama_shift', 'NON SHIFT')->first();
            $rows = collect();

            foreach ($employees->sortBy('name') as $employee) {
                foreach ($period as $date) {
                    $row = $this->attendanceRow($employee, $assignmentMap->get($employee->id), $date->copy(), $mappingShifts->get($employee->id, collect()), $nonShift);
                    $rows->push(array_values([
                        $row['tanggal'],
                        $row['hari'],
                        $row['work_force_id'],
                        $row['nama_karyawan'],
                        $row['jabatan'],
                        $row['departemen'],
                        $row['divisi'],
                        $row['bagian'],
                        $row['shift'],
                        $row['masuk'],
                        $row['pulang'],
                        $row['status'],
                    ]));
                }
            }

            $fileName = 'laporan-presensi-bawahan-' . $holdingData->holding_code . '.xlsx';
            $filePath = storage_path('app/' . $fileName);
            $writer = new Writer();
            $writer->openToFile($filePath);
            $writer->addRow(Row::fromValues([
                'Tanggal',
                'Hari',
                'ID Karyawan',
                'Nama Karyawan',
                'Jabatan',
                'Departemen',
                'Divisi',
                'Bagian',
                'Shift',
                'Masuk',
                'Pulang',
                'Status',
            ]));
            foreach ($rows as $row) {
                $writer->addRow(Row::fromValues($row));
            }
            $writer->close();

            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function getSidebarMenus()
    {
        $roleIds = RoleUsers::where('role_user_id', Auth::user()->id)
            ->pluck('role_menu_id')
            ->toArray();

        if (count($roleIds) === 0) {
            return collect();
        }

        return Menu::whereIn('id', function ($query) use ($roleIds) {
            $query->select('menu_id')
                ->from('role_menus')
                ->whereIn('role_id', $roleIds);
        })
            ->whereNull('parent_id')
            ->with('children')
            ->where('kategori', 'web')
            ->orderBy('sort_order')
            ->get();
    }

    private function employeeAssignments(string $holdingId): Collection
    {
        return KaryawanJabatan::with([
            'Karyawan',
            'Jabatan.LevelJabatan',
            'Departemen',
            'Divisi',
            'Bagian',
        ])
            ->where('holding_id', $holdingId)
            ->where('is_active', 1)
            ->where('main', 1)
            ->whereHas('Karyawan', fn($query) => $query->where('status_aktif', 'AKTIF'))
            ->get();
    }

    private function allEmployeeAssignments(): Collection
    {
        return KaryawanJabatan::with([
            'Karyawan',
            'Jabatan.LevelJabatan',
            'Departemen',
            'Divisi',
            'Bagian',
        ])
            ->where('is_active', 1)
            ->where('main', 1)
            ->whereHas('Karyawan', fn($query) => $query->where('status_aktif', 'AKTIF'))
            ->get()
            ->unique('karyawan_id')
            ->values();
    }

    private function employeePayload(KaryawanJabatan $assignment): array
    {
        $employee = $assignment->Karyawan;
        $jabatan = $assignment->Jabatan;

        return [
            'id' => $employee->id,
            'work_force_id' => $employee->nomor_identitas_karyawan,
            'name' => $employee->name,
            'jabatan_id' => $assignment->jabatan_id,
            'role' => $jabatan->nama_jabatan ?? '-',
            'title' => $jabatan?->LevelJabatan?->level_name ?? '-',
            'level' => $jabatan?->LevelJabatan?->level_jabatan,
            'departemen' => $assignment->Departemen?->nama_departemen ?? '-',
            'divisi' => $assignment->Divisi?->nama_divisi ?? '-',
            'bagian' => $assignment->Bagian?->nama_bagian ?? '-',
            'reports_to_jabatan_id' => $jabatan?->atasan_id,
            'reports_to2_jabatan_id' => $jabatan?->atasan2_id,
        ];
    }

    private function downlineIds(Collection $assignments, KaryawanJabatan $rootAssignment, string $holdingId): Collection
    {
        $strukturIds = $this->strukturDownlineIds($rootAssignment, $holdingId)
            ->reject(fn($employeeId) => $employeeId === $rootAssignment->karyawan_id)
            ->values();

        if ($strukturIds->isNotEmpty()) {
            return $strukturIds;
        }

        $rootLevel = (int) ($rootAssignment->Jabatan?->LevelJabatan?->level_jabatan ?? 99);

        if ($rootLevel <= 0) {
            return $assignments
                ->pluck('karyawan_id')
                ->reject(fn($employeeId) => $employeeId === $rootAssignment->karyawan_id)
                ->unique()
                ->values();
        }

        $visitedJabatanIds = collect();
        $employeeIds = collect();
        $queue = collect([$rootAssignment->jabatan_id]);

        while ($queue->isNotEmpty()) {
            $jabatanId = $queue->shift();

            if (!$jabatanId || $visitedJabatanIds->contains($jabatanId)) {
                continue;
            }

            $visitedJabatanIds->push($jabatanId);

            $children = $assignments->filter(function (KaryawanJabatan $assignment) use ($jabatanId) {
                return in_array($jabatanId, [
                    $assignment->Jabatan?->atasan_id,
                    $assignment->Jabatan?->atasan2_id,
                ], true);
            });

            foreach ($children as $child) {
                if ($child->Karyawan) {
                    $employeeIds->push($child->karyawan_id);
                }

                if ($child->jabatan_id && !$visitedJabatanIds->contains($child->jabatan_id)) {
                    $queue->push($child->jabatan_id);
                }
            }
        }

        return $employeeIds->unique()->values();
    }

    private function savedDownlineIds(string $holdingId, $rootId): Collection
    {
        $relations = PresensiBawahanRelation::where('holding_id', $holdingId)->get()->groupBy('atasan_id');
        $visited = collect();
        $downlineIds = collect();
        $queue = collect([(string) $rootId]);

        while ($queue->isNotEmpty()) {
            $atasanId = $queue->shift();
            if ($visited->contains($atasanId)) {
                continue;
            }
            $visited->push($atasanId);

            foreach ($relations->get($atasanId, collect()) as $relation) {
                $bawahanId = (string) $relation->bawahan_id;
                if (!$downlineIds->contains($bawahanId)) {
                    $downlineIds->push($bawahanId);
                    $queue->push($bawahanId);
                }
            }
        }

        return $downlineIds;
    }

    private function directDownlines(Collection $assignments, KaryawanJabatan $selectedAssignment, string $holdingId): Collection
    {
        $savedRelations = PresensiBawahanRelation::where('holding_id', $holdingId)
            ->where('atasan_id', $selectedAssignment->karyawan_id)
            ->get();

        if ($savedRelations->isNotEmpty() || PresensiBawahanRelation::where('holding_id', $holdingId)->exists()) {
            return $assignments
                ->whereIn('karyawan_id', $savedRelations->pluck('bawahan_id'))
                ->sortBy(fn(KaryawanJabatan $assignment) => $assignment->Karyawan->name ?? '')
                ->values()
                ->map(fn(KaryawanJabatan $assignment) => $this->employeePayload($assignment));
        }

        $strukturIds = $this->strukturDirectDownlineIds($selectedAssignment, $holdingId);

        if ($strukturIds->isNotEmpty()) {
            return $assignments
                ->whereIn('karyawan_id', $strukturIds)
                ->sortBy(fn(KaryawanJabatan $assignment) => $assignment->Karyawan->name ?? '')
                ->values()
                ->map(fn(KaryawanJabatan $assignment) => $this->employeePayload($assignment));
        }

        return $assignments
            ->filter(function (KaryawanJabatan $assignment) use ($selectedAssignment) {
                return in_array($selectedAssignment->jabatan_id, [
                    $assignment->Jabatan?->atasan_id,
                    $assignment->Jabatan?->atasan2_id,
                ], true);
            })
            ->sortBy(fn(KaryawanJabatan $assignment) => $assignment->Karyawan->name ?? '')
            ->values()
            ->map(fn(KaryawanJabatan $assignment) => $this->employeePayload($assignment));
    }

    private function structureDirectDownlines(Collection $assignments, KaryawanJabatan $selectedAssignment, string $holdingId): Collection
    {
        $strukturIds = $this->strukturDirectDownlineIds($selectedAssignment, $holdingId);

        if ($strukturIds->isNotEmpty()) {
            return $assignments
                ->whereIn('karyawan_id', $strukturIds)
                ->sortBy(fn(KaryawanJabatan $assignment) => $assignment->Karyawan->name ?? '')
                ->values()
                ->map(fn(KaryawanJabatan $assignment) => $this->employeePayload($assignment));
        }

        return $assignments
            ->filter(function (KaryawanJabatan $assignment) use ($selectedAssignment) {
                return in_array($selectedAssignment->jabatan_id, [
                    $assignment->Jabatan?->atasan_id,
                    $assignment->Jabatan?->atasan2_id,
                ], true);
            })
            ->sortBy(fn(KaryawanJabatan $assignment) => $assignment->Karyawan->name ?? '')
            ->values()
            ->map(fn(KaryawanJabatan $assignment) => $this->employeePayload($assignment));
    }

    private function uplines(Collection $assignments, KaryawanJabatan $selectedAssignment, string $holdingId): Collection
    {
        $savedIds = PresensiBawahanRelation::where('holding_id', $holdingId)
            ->where('bawahan_id', $selectedAssignment->karyawan_id)
            ->pluck('atasan_id');

        if ($savedIds->isNotEmpty() || PresensiBawahanRelation::where('holding_id', $holdingId)->exists()) {
            return $assignments
                ->whereIn('karyawan_id', $savedIds)
                ->sortBy(fn(KaryawanJabatan $assignment) => $assignment->Karyawan->name ?? '')
                ->values()
                ->map(fn(KaryawanJabatan $assignment) => $this->employeePayload($assignment));
        }

        $strukturIds = $this->strukturUplineIds($selectedAssignment, $holdingId);

        if ($strukturIds->isNotEmpty()) {
            return $assignments
                ->whereIn('karyawan_id', $strukturIds)
                ->sortBy(fn(KaryawanJabatan $assignment) => $assignment->Karyawan->name ?? '')
                ->values()
                ->map(fn(KaryawanJabatan $assignment) => $this->employeePayload($assignment));
        }

        $reportsToJabatanIds = collect([
            $selectedAssignment->Jabatan?->atasan_id,
            $selectedAssignment->Jabatan?->atasan2_id,
        ])->filter()->unique()->values();

        return $assignments
            ->whereIn('jabatan_id', $reportsToJabatanIds)
            ->sortBy(fn(KaryawanJabatan $assignment) => $assignment->Karyawan->name ?? '')
            ->values()
            ->map(fn(KaryawanJabatan $assignment) => $this->employeePayload($assignment));
    }

    private function strukturDownlineIds(KaryawanJabatan $assignment, string $holdingId): Collection
    {
        $nodes = StrukturModel::with('ChildrenRecursive.UserKaryawan')
            ->where('holding', $holdingId)
            ->where('jabatan', $assignment->jabatan_id)
            ->get();

        if ($nodes->isEmpty()) {
            return collect();
        }

        return $this->collectStrukturEmployeeIds($nodes);
    }

    private function collectStrukturEmployeeIds($struktur): Collection
    {
        if ($struktur instanceof StrukturModel) {
            $struktur = collect([$struktur]);
        }

        $ids = collect();

        foreach ($struktur as $node) {
            if ($node->UserKaryawan->isNotEmpty()) {
                $ids = $ids->merge($node->UserKaryawan->pluck('karyawan_id'));
            }

            if ($node->ChildrenRecursive->isNotEmpty()) {
                $ids = $ids->merge($this->collectStrukturEmployeeIds($node->ChildrenRecursive));
            }
        }

        return $ids->unique()->values();
    }

    private function strukturDirectDownlineIds(KaryawanJabatan $assignment, string $holdingId): Collection
    {
        $nodes = StrukturModel::with('Children.UserKaryawan')
            ->where('holding', $holdingId)
            ->where('jabatan', $assignment->jabatan_id)
            ->get();

        if ($nodes->isEmpty()) {
            return collect();
        }

        return $nodes
            ->flatMap(fn(StrukturModel $node) => $node->Children)
            ->flatMap(fn(StrukturModel $child) => $child->UserKaryawan)
            ->pluck('karyawan_id')
            ->unique()
            ->values();
    }

    private function strukturUplineIds(KaryawanJabatan $assignment, string $holdingId): Collection
    {
        $nodes = StrukturModel::where('holding', $holdingId)
            ->where('jabatan', $assignment->jabatan_id)
            ->whereNotNull('parent')
            ->get();

        if ($nodes->isEmpty()) {
            return collect();
        }

        $parentIds = $nodes->pluck('parent')->filter()->unique()->values();

        return StrukturModel::with('UserKaryawan')
            ->whereIn('id', $parentIds)
            ->get()
            ->flatMap(fn(StrukturModel $node) => $node->UserKaryawan)
            ->pluck('karyawan_id')
            ->unique()
            ->values();
    }

    private function attendanceRow(
        Karyawan $employee,
        ?KaryawanJabatan $assignment,
        Carbon $date,
        Collection $mappingShifts,
        ?Shift $nonShift
    ): array {
        $calendar = Calendar::where('start_date', $date->format('Y-m-d'))
            ->where('status', 1)
            ->first();

        $cutiDetail = CutiDetail::where('id_karyawan', $employee->id)
            ->whereDate('tgl_cuti', $date->toDateString())
            ->where('status', 1)
            ->exists();

        $sakitDetail = IzinSakitDetail::where('id_karyawan', $employee->id)
            ->whereDate('tgl_sakit', $date->toDateString())
            ->where('status', 1)
            ->exists();

        $tukarLibur = IzinLibur::where('karyawan_id', $employee->id)
            ->whereDate('tanggal_tukar', $date->toDateString())
            ->where('status_libur', 2)
            ->exists();

        $logs = $employee->Absensi->filter(function (AttendanceLog $log) use ($date) {
            return Carbon::parse($log->LogTime)->isSameDay($date);
        });

        $checkIn = '';
        $checkOut = '';
        $status = '';
        $shiftName = '-';
        $statusGroup = 'kosong';

        if ($calendar) {
            return $this->baseAttendancePayload($employee, $assignment, $date, [
                'shift' => 'Kalender',
                'masuk' => '',
                'pulang' => '',
                'status' => $calendar->nama_kegiatan,
                'status_group' => 'libur',
            ]);
        }
        if ($employee->shift === 'NON SHIFT' && $nonShift) {
            $shiftName = $nonShift->nama_shift;
            $startMasuk = Carbon::parse($date->format('Y-m-d') . ' ' . $nonShift->jam_min_masuk);
            $endMasuk = Carbon::parse($date->format('Y-m-d') . ' ' . $nonShift->jam_terlambat)->addHours(3)->addMinutes(10);
            $startPulang = Carbon::parse($date->format('Y-m-d') . ' ' . $nonShift->jam_pulang_cepat);
            $endPulang = Carbon::parse($date->format('Y-m-d') . ' ' . $nonShift->jam_keluar)->addHours(5);
            $masukLogs = $logs->filter(fn($log) => Carbon::parse($log->LogTime)->between($startMasuk, $endMasuk));
            $awalLogs = $logs->filter(fn($log) => Carbon::parse($log->LogTime)->between($date->copy()->startOfDay(), $startMasuk));
            $pulangLogs = $logs->filter(fn($log) => Carbon::parse($log->LogTime)->between($startPulang, $endPulang));

            if ($masukLogs->isNotEmpty()) {
                $checkIn = Carbon::parse($masukLogs->min('LogTime'))->format('H:i');
                $status = $checkIn > $nonShift->jam_terlambat ? 'TELAT' : 'TEPAT WAKTU';
                $statusGroup = 'hadir';
            } elseif ($awalLogs->isNotEmpty()) {
                $checkIn = Carbon::parse($awalLogs->min('LogTime'))->format('H:i');
                $status = 'DATANG LEBIH AWAL';
                $statusGroup = 'hadir';
            } else {
                [$checkIn, $checkOut, $status, $statusGroup] = $this->emptyAttendanceStatus(
                    $date,
                    (int) $nonShift->hari_libur,
                    $cutiDetail,
                    $sakitDetail,
                    $tukarLibur
                );
            }

            if ($pulangLogs->isNotEmpty()) {
                $checkOut = Carbon::parse($pulangLogs->max('LogTime'))->format('H:i');
            } elseif ((int) $nonShift->hari_libur === $date->dayOfWeek) {
                $checkOut = 'LIBUR';
            }
        } else {
            $mapping = $mappingShifts->firstWhere('tanggal_masuk', $date->toDateString());

            if (!$mapping) {
                if ($cutiDetail) {
                    $checkIn = 'CUTI';
                    $checkOut = 'CUTI';
                    $status = 'CUTI';
                    $statusGroup = 'izin';
                } elseif ($sakitDetail) {
                    $checkIn = 'SAKIT';
                    $checkOut = 'SAKIT';
                    $status = 'SAKIT';
                    $statusGroup = 'izin';
                } else {
                    $status = 'BELUM DIJADWALKAN';
                    $statusGroup = 'belum_dijadwalkan';
                }
            } elseif ($mapping->status_absen === 'LIBUR') {
                $shiftName = $mapping->Shift?->nama_shift ?? $mapping->nama_shift ?? 'SHIFT';
                $checkIn = 'LIBUR';
                $checkOut = 'LIBUR';
                $status = 'LIBUR';
                $statusGroup = 'libur';
            } else {
                $shift = $mapping->Shift;
                $shiftName = $shift?->nama_shift ?? $mapping->nama_shift ?? 'SHIFT';

                if ($shift) {
                    $startMasuk = Carbon::parse($date->format('Y-m-d') . ' ' . $shift->jam_min_masuk);
                    $batasAwal = Carbon::parse($date->format('Y-m-d') . ' ' . $shift->jam_min_masuk)->subHours(3);
                    $endMasuk = Carbon::parse($date->format('Y-m-d') . ' ' . $shift->jam_terlambat)->addHours(3)->addMinutes(10);
                    $startPulang = Carbon::parse($mapping->tanggal_pulang . ' ' . $shift->jam_pulang_cepat);
                    $endPulang = Carbon::parse($mapping->tanggal_pulang . ' ' . $shift->jam_keluar)->addHours(5);
                    $masukLogs = $logs->filter(fn($log) => Carbon::parse($log->LogTime)->between($startMasuk, $endMasuk));
                    $awalLogs = $logs->filter(fn($log) => Carbon::parse($log->LogTime)->between($batasAwal, $startMasuk));
                    $pulangLogs = $logs->filter(fn($log) => Carbon::parse($log->LogTime)->between($startPulang, $endPulang));

                    if ($masukLogs->isNotEmpty()) {
                        $checkIn = Carbon::parse($masukLogs->min('LogTime'))->format('H:i');
                        $status = $checkIn > $shift->jam_terlambat ? 'TELAT' : 'TEPAT WAKTU';
                        $statusGroup = 'hadir';
                    } elseif ($awalLogs->isNotEmpty()) {
                        $checkIn = Carbon::parse($awalLogs->min('LogTime'))->format('H:i');
                        $status = 'DATANG LEBIH AWAL';
                        $statusGroup = 'hadir';
                    } else {
                        [$checkIn, $checkOut, $status, $statusGroup] = $this->emptyAttendanceStatus(
                            $date,
                            (int) $shift->hari_libur,
                            $cutiDetail,
                            $sakitDetail,
                            $tukarLibur
                        );
                    }

                    if ($pulangLogs->isNotEmpty()) {
                        $checkOut = Carbon::parse($pulangLogs->max('LogTime'))->format('H:i');
                    } elseif ((int) $shift->hari_libur === $date->dayOfWeek) {
                        $checkOut = 'LIBUR';
                    }
                } else {
                    $status = 'BELUM DIJADWALKAN';
                    $statusGroup = 'belum_dijadwalkan';
                }
            }
        }

        return $this->baseAttendancePayload($employee, $assignment, $date, [
            'shift' => $shiftName,
            'masuk' => $checkIn,
            'pulang' => $checkOut,
            'status' => $status,
            'status_group' => $statusGroup,
        ]);
    }

    private function emptyAttendanceStatus(
        Carbon $date,
        int $hariLibur,
        bool $cutiDetail,
        bool $sakitDetail,
        bool $tukarLibur
    ): array {
        if ($hariLibur === $date->dayOfWeek) {
            return ['LIBUR', 'LIBUR', 'LIBUR', 'libur'];
        }

        if ($cutiDetail) {
            return ['CUTI', 'CUTI', 'CUTI', 'izin'];
        }

        if ($sakitDetail) {
            return ['SAKIT', 'SAKIT', 'SAKIT', 'izin'];
        }

        if ($tukarLibur) {
            return ['TUKAR LIBUR', 'TUKAR LIBUR', 'TUKAR LIBUR', 'izin'];
        }

        return ['', '', $date->isFuture() ? '' : 'TIDAK HADIR', $date->isFuture() ? 'kosong' : 'tidak_hadir'];
    }

    private function baseAttendancePayload(Karyawan $employee, ?KaryawanJabatan $assignment, Carbon $date, array $data): array
    {
        return [
            'tanggal' => $date->format('Y-m-d'),
            'hari' => $date->locale('id')->isoFormat('dddd'),
            'karyawan_id' => $employee->id,
            'work_force_id' => $employee->nomor_identitas_karyawan,
            'nama_karyawan' => $employee->name,
            'jabatan' => $assignment?->Jabatan?->nama_jabatan ?? '-',
            'departemen' => $assignment?->Departemen?->nama_departemen ?? '-',
            'divisi' => $assignment?->Divisi?->nama_divisi ?? '-',
            'bagian' => $assignment?->Bagian?->nama_bagian ?? '-',
            'shift' => $data['shift'],
            'masuk' => $data['masuk'],
            'pulang' => $data['pulang'],
            'status' => $data['status'],
            'status_group' => $data['status_group'],
        ];
    }

    private function errorResponse(\Exception $e)
    {
        return response()->json([
            'code' => 500,
            'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
        ], 500);
    }
}
