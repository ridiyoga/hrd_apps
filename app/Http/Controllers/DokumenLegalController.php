<?php

namespace App\Http\Controllers;

use App\Models\DokumenLegal;
use App\Models\KategoriDokumen;
use App\Models\PenerbitDokumen;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DokumenLegalController extends Controller
{
    public function index(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        // ===== MENU (WAJIB UNTUK SIDEBAR) =====
        $roleIds = RoleUsers::where('role_user_id', Auth::user()->id)
            ->pluck('role_menu_id')
            ->toArray();

        $menus = Menu::whereIn('id', function ($q) use ($roleIds) {
            $q->select('menu_id')
                ->from('role_menus')
                ->whereIn('role_id', $roleIds);
        })
            ->whereNull('parent_id')
            ->where('kategori', 'web')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $sites = Site::orderBy('site_name')->get();

        $kategoriFilter = $request->input('kategori_filter', '');
        $penerbitFilter = $request->input('penerbit_filter', '');
        $statusFilter = $request->input('status_filter', 'all');
        $today = Carbon::today();

        $statusTabs = [
            'all' => 'All',
            'aktif' => 'Aktif',
            'expired' => 'Expired',
            'digantikan' => 'Digantikan',
            'tidak_diperpanjang' => 'Tidak Diperpanjang',
        ];

        if (! array_key_exists($statusFilter, $statusTabs)) {
            $statusFilter = 'all';
        }

        // ===== DATA DOKUMEN (NON SERVER SIDE) =====
        $baseDocumentsQuery = DokumenLegal::where('holding_id', $holding->id)
            ->when($kategoriFilter, function ($q) use ($kategoriFilter) {
                $q->where('kategori_id', $kategoriFilter);
            })
            ->when($penerbitFilter, function ($q) use ($penerbitFilter) {
                $q->where('penerbit_id', $penerbitFilter);
            });

        $statusCounts = [
            'all' => (clone $baseDocumentsQuery)->count(),
            'aktif' => (clone $baseDocumentsQuery)
                ->where('status', 'aktif')
                ->whereDate('tanggal_akhir', '>=', $today)
                ->count(),
            'expired' => (clone $baseDocumentsQuery)
                ->where(function ($query) use ($today) {
                    $query->where('status', 'expired')
                        ->orWhere(function ($query) use ($today) {
                            $query->where('status', 'aktif')
                                ->whereDate('tanggal_akhir', '<', $today);
                        });
                })
                ->count(),
            'digantikan' => (clone $baseDocumentsQuery)
                ->where('status', 'digantikan')
                ->count(),
            'tidak_diperpanjang' => (clone $baseDocumentsQuery)
                ->where('status', 'tidak_diperpanjang')
                ->count(),
        ];

        $documents = (clone $baseDocumentsQuery)
            ->when($statusFilter === 'aktif', function ($query) use ($today) {
                $query->where('status', 'aktif')
                    ->whereDate('tanggal_akhir', '>=', $today);
            })
            ->when($statusFilter === 'expired', function ($query) use ($today) {
                $query->where(function ($query) use ($today) {
                    $query->where('status', 'expired')
                        ->orWhere(function ($query) use ($today) {
                            $query->where('status', 'aktif')
                                ->whereDate('tanggal_akhir', '<', $today);
                        });
                });
            })
            ->when(in_array($statusFilter, ['digantikan', 'tidak_diperpanjang'], true), function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->orderBy('id', 'desc')
            ->get();

        $kategoriList = KategoriDokumen::orderBy('nama_kategori')->get();
        $penerbitList = PenerbitDokumen::orderBy('nama_penerbit')->get();

        //dd($documents->toArray());

        return view('admin.dokumen_legal.index', compact(
            'holding',
            'menus',
            'documents',
            'sites',
            'kategoriList',
            'penerbitList',
            'statusFilter',
            'statusTabs',
            'statusCounts',
            'kategoriFilter',
            'penerbitFilter',
        ));
    }

    public function create(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        // ===== MENU SIDEBAR =====
        $roleIds = RoleUsers::where('role_user_id', Auth::id())
            ->pluck('role_menu_id')
            ->toArray();

        $menus = Menu::whereIn('id', function ($q) use ($roleIds) {
            $q->select('menu_id')
                ->from('role_menus')
                ->whereIn('role_id', $roleIds);
        })
            ->whereNull('parent_id')
            ->where('kategori', 'web')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        // ===== MASTER DATA =====
        $sites = Site::orderBy('site_name')->get();
        $kategori = KategoriDokumen::orderBy('nama_kategori')->get();
        $penerbitDokumen = collect();

        if ($request->filled('kategori_id')) {
            $penerbitDokumen = PenerbitDokumen::where(
                'kategori_dokumen_id',
                $request->kategori_id
            )->orderBy('nama_penerbit')->get();
        }

        // DOKUMEN AKTIF (UNTUK REFERENCE)
        $documents = DokumenLegal::where('holding_id', $holding->id)
            ->whereIn('status', ['aktif', 'expired'])
            ->orderBy('nomer_dukumen')
            ->get();
        //dd($documents->all());
        return view('admin.dokumen_legal.create', compact(
            'holding',
            'menus',
            'sites',
            'kategori',
            'penerbitDokumen',
            'documents'
        ));
    }

    public function ajaxPenerbit($kategoriId)
    {
        return PenerbitDokumen::where('kategori_dokumen_id', $kategoriId)
            ->orderBy('nama_penerbit')
            ->get([
                'id_penerbit',
                'nama_penerbit',
            ]);
    }

    public function ajaxReference(Request $request)
    {
        $request->validate([
            'holding_id'  => 'required',
            'kategori_id' => 'required',
        ]);

        $query = DokumenLegal::query()
            ->where('holding_id', $request->holding_id)
            ->where('kategori_id', $request->kategori_id)
            ->whereIn('status', ['aktif', 'expired']);

        // FILTER SEARCH (TIDAK CASE SENSITIVE)
        if ($request->filled('q')) {
            $keyword = strtolower($request->q);

            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(nomer_dukumen) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(nama) LIKE ?', ["%{$keyword}%"]);
            });
        }

        $docs = $query
            ->orderBy('nomer_dukumen')
            ->limit(25)
            ->get();

        return response()->json(
            $docs->map(fn($d) => [
                'id'   => $d->id,
                'text' => $d->nomer_dukumen . ' - ' . $d->nama
                    . ($d->status === 'expired' ? ' (Expired)' : ''),
            ])
        );
    }

    public function ajaxPenerbitByKategori($kategoriId)
    {
        return PenerbitDokumen::where('kategori_dokumen_id', $kategoriId)
            ->orderBy('nama_penerbit')
            ->get([
                'id_penerbit as id',
                'nama_penerbit as text'
            ]);
    }

    public function ajaxKategoriByPenerbit($penerbitId)
    {
        $kategori = KategoriDokumen::whereHas('penerbit', function ($q) use ($penerbitId) {
            $q->where('id_penerbit', $penerbitId);
        })->get([
            'id_kategori as id',
            'nama_kategori as text'
        ]);

        return $kategori;
    }

    public function store(Request $request, $holdingCode)
    {
        //dd($request->all());
        $holding = Holding::where('holding_code', $holdingCode)->firstOrFail();

        // =========================
        // VALIDASI BACKEND
        // =========================
        $request->validate([
            'kategori_id'           => 'required|integer|exists:kategori_dokumen_legal,id_kategori',
            'sub_kategori_id'       => 'nullable|exists:sub_kategori_dokumen_legal,id_sub',

            // VALIDASI PENERBIT SESUAI KATEGORI
            'penerbit_id' => [
                'required',
                Rule::exists('penerbit_dokumen', 'id_penerbit')
                    ->where('kategori_dokumen_id', $request->kategori_id),
            ],

            'is_dokumen_pengganti'  => 'boolean',
            'dokumen_reference'     => 'required_if:is_dokumen_pengganti,1',
            'nomer_dukumen'         => 'required|string|max:100',
            'nama'                  => 'required|string|max:255',
            'site_id'               => 'required|exists:sites,id',
            'status'                => 'required|string',
            'tanggal_awal'          => 'required|date',
            'tanggal_akhir'         => 'required|date',
            'file'                  => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->is_dokumen_pengganti) {
            $ref = DokumenLegal::findOrFail($request->dokumen_reference);

            if ($ref->kategori_id != $request->kategori_id) {
                return back()->withErrors([
                    'dokumen_reference' => 'Dokumen yang digantikan harus dari kategori yang sama.'
                ])->withInput();
            }
        }

        DB::beginTransaction();

        try {
            // =========================
            // 1️⃣ GENERATE ID MANUAL
            // =========================
            $nextId = DokumenLegal::max('id') + 1;

            // =========================
            // 2️⃣ UPLOAD FILE
            // =========================
            $path = null;
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('dokumen_legal', 'public');
            }

            // =========================
            // 3️⃣ SIMPAN DOKUMEN BARU
            // =========================
            $dokumenBaru = DokumenLegal::create([
                'id'                    => $nextId,
                'nomer_dukumen'         => $request->nomer_dukumen,
                'nama'                  => $request->nama,
                'file'                  => $path,
                'tanggal_awal'          => $request->tanggal_awal,
                'tanggal_akhir'         => $request->tanggal_akhir,
                'keterangan'            => $request->keterangan,
                'status'                => 'aktif',

                'kategori_id'           => $request->kategori_id,
                'sub_kategori_id'       => $request->sub_kategori_id,
                'penerbit_id'           => $request->penerbit_id,

                'holding_id'            => $holding->id,
                'site_id'               => $request->site_id,

                // 🔁 fitur pengganti
                'is_dokumen_pengganti'  => $request->is_dokumen_pengganti,
                'dokumen_reference'     => $request->is_dokumen_pengganti
                    ? $request->dokumen_reference
                    : null,
            ]);

            // =========================
            // 4️⃣ JIKA DOKUMEN PENGGANTI
            // =========================
            if ($request->is_dokumen_pengganti) {

                $dokumenLama = DokumenLegal::lockForUpdate()
                    ->where('id', $request->dokumen_reference)
                    ->firstOrFail();

                $dokumenLama->update([
                    'status'             => 'digantikan',
                    'dokumen_pengganti'  => $dokumenBaru->id,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('dokumen_legal.index', $holding->holding_code)
                ->with('success', 'Dokumen berhasil disimpan');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors('Gagal menyimpan dokumen: ' . $e->getMessage());
        }
    }

    public function show($holding, $id)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        // menu sidebar (WAJIB biar layout tidak error)
        $roleIds = RoleUsers::where('role_user_id', Auth::user()->id)
            ->pluck('role_menu_id')
            ->toArray();

        $menus = Menu::whereIn('id', function ($q) use ($roleIds) {
            $q->select('menu_id')
                ->from('role_menus')
                ->whereIn('role_id', $roleIds);
        })
            ->whereNull('parent_id')
            ->where('kategori', 'web')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $sites = Site::orderBy('site_name')->get();
        $kategori = KategoriDokumen::orderBy('nama_kategori')->get();

        $document = DokumenLegal::with([
            'kategori',
            'subKategori',
            'site',
            'penerbit',        // 🔥 WAJIB
            'dokumenLama',
            'dokumenPengganti',
        ])
            ->where('id', $id)
            ->where('holding_id', $holding->id)
            ->firstOrFail();

        //dd( $document->toArray());    
        return view('admin.dokumen_legal.show', [
            'holding'  => $holding,
            'menus'    => $menus,
            'document' => $document,
            'sites'     => $sites,
            'kategori'  => $kategori,
        ]);
    }

    public function setTidakDiperpanjang($holding, $id)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        $dokumen = DokumenLegal::where('id', $id)
            ->where('holding_id', $holding->id)
            ->where(function ($query) {
                $query->where('status', 'expired')
                    ->orWhere(function ($query) {
                        $query->where('status', 'aktif')
                            ->whereDate('tanggal_akhir', '<', Carbon::today());
                    });
            })
            ->firstOrFail();

        $dokumen->update([
            'status' => 'tidak_diperpanjang'
        ]);

        return redirect()
            ->back()
            ->with('success', 'Dokumen ditandai sebagai tidak diperpanjang');
    }

    public function edit($holding, $id)
    {
        // ================= HOLDING =================
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        // ================= MENU SIDEBAR =================
        $roleIds = RoleUsers::where('role_user_id', Auth::id())
            ->pluck('role_menu_id')
            ->toArray();

        $menus = Menu::whereIn('id', function ($q) use ($roleIds) {
            $q->select('menu_id')
                ->from('role_menus')
                ->whereIn('role_id', $roleIds);
        })
            ->whereNull('parent_id')
            ->where('kategori', 'web')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        // ================= DOKUMEN =================
        $document = DokumenLegal::where('id', $id)
            ->where('holding_id', $holding->id)
            ->firstOrFail();

        // Dokumen hanya bisa diedit jika masih aktif
        if ($document->status !== 'aktif') {
            abort(403, 'Dokumen ini tidak bisa diedit karena sudah digantikan.');
        }

        // ================= MASTER DATA =================
        $sites = Site::orderBy('site_name')->get();
        $kategori = KategoriDokumen::orderBy('nama_kategori')->get();

        // ================= PENERBIT (FILTER SESUAI KATEGORI DOKUMEN) =================
        $penerbitDokumen = PenerbitDokumen::where(
            'kategori_dokumen_id',
            $document->kategori_id
        )
            ->orderBy('nama_penerbit')
            ->get();

        // ================= VIEW =================
        return view('admin.dokumen_legal.edit', [
            'holding'         => $holding,
            'menus'           => $menus,
            'document'        => $document,
            'sites'           => $sites,
            'kategori'        => $kategori,
            'penerbitDokumen' => $penerbitDokumen,
        ]);
    }

    public function update(Request $request, $holding, $id)
    {
        // ================= HOLDING =================
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        // ================= DOKUMEN =================
        $document = DokumenLegal::where('id', $id)
            ->where('holding_id', $holding->id)
            ->firstOrFail();

        // ================= VALIDASI =================
        $validated = $request->validate([
            'kategori_id'       => ['required', 'integer', 'exists:kategori_dokumen_legal,id_kategori',],
            'sub_kategori_id'   => ['nullable', 'exists:sub_kategori_dokumen_legal,id_sub',],
            'penerbit_id'              => [
                'required',
                Rule::exists('penerbit_dokumen', 'id_penerbit')
                    ->where('kategori_dokumen_id', $request->kategori_id),
            ],
            'nomer_dukumen'     => ['required', 'string', 'max:100',],
            'nama'              => ['required', 'string', 'max:255',],
            'site_id'           => ['required', 'exists:sites,id',],
            'status'            => ['required', 'in:aktif,expired',],
            'tanggal_awal'      => ['required', 'date',],
            'tanggal_akhir'     => ['required', 'date', 'after_or_equal:tanggal_awal',],
            'keterangan'        => ['nullable', 'string', 'max:255',],
            'file'              => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120',],
        ]);

        // ================= FILE HANDLING =================
        if ($request->hasFile('file')) {

            // hapus file lama jika ada
            if ($document->file && Storage::disk('public')->exists($document->file)) {
                Storage::disk('public')->delete($document->file);
            }

            $validated['file'] = $request->file('file')
                ->store('dokumen_legal', 'public');
        } else {
            // jangan timpa kolom file
            unset($validated['file']);
        }

        // ================= UPDATE DATA =================
        $document->update($validated);

        // ================= RESPONSE =================
        return redirect()
            ->route('dokumen_legal.index', $holding->holding_code)
            ->with('success', 'Dokumen berhasil diupdate');
    }

    public function destroy($holdingCode, $id)
    {
        $holding = Holding::where('holding_code', $holdingCode)->firstOrFail();

        $dokumen = DokumenLegal::where('id', $id)
            ->where('holding_id', $holding->id)
            ->firstOrFail();

        DB::beginTransaction();

        try {
            // ================================
            // JIKA INI DOKUMEN PENGGANTI
            // ================================
            if ($dokumen->dokumen_reference) {

                $dokumenLama = DokumenLegal::lockForUpdate()
                    ->find($dokumen->dokumen_reference);

                if ($dokumenLama) {
                    $dokumenLama->update([
                        'dokumen_pengganti' => null,
                        'status' => $dokumenLama->hitungStatusByTanggal(),
                    ]);
                }
            }

            // ================================
            // HAPUS FILE
            // ================================
            if ($dokumen->file && Storage::disk('public')->exists($dokumen->file)) {
                Storage::disk('public')->delete($dokumen->file);
            }

            // ================================
            // HAPUS DATA
            // ================================
            $dokumen->delete();

            DB::commit();

            return redirect()
                ->route('dokumen_legal.index', $holdingCode)
                ->with('success', 'Dokumen berhasil dihapus dan status dokumen lama diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors('Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    public function showBySpsCode(string $spscode)
    {
        $dokumenLegal = \App\Models\DokumenLegal::whereRaw(
            'dokumen_legal.nomer_dukumen = ?',
            [$spscode]
        )->firstOrFail();

        return view('admin.dokumen_legal.show', compact('legalDocument'));
    }
}
