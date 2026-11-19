<?php

namespace App\Http\Controllers;

use App\Models\PenerbitDokumen;
use App\Models\KategoriDokumen;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\RoleUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenerbitDokumenController extends Controller
{
    /* ================= INDEX ================= */
    public function index($holding)
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

        // ===== DATA =====
        $penerbitDokumen = PenerbitDokumen::with('kategoriDokumen')
            ->orderBy('nama_penerbit')
            ->get();

        return view('admin.penerbit_dokumen.index', compact(
            'holding',
            'menus',
            'penerbitDokumen'
        ));
    }

    /* ================= CREATE ================= */
    public function create($holding)
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

        $kategoriDokumen = KategoriDokumen::orderBy('nama_kategori')->get();

        return view('admin.penerbit_dokumen.create', [
            'holding'         => $holding,
            'menus'           => $menus,
            'kategoriDokumen' => $kategoriDokumen,
            'kategori'        => null,
            'isFromKategori'  => false,
        ]);
    }

    /* ================= CREATE FROM KATEGORI ================= */
    public function createFromKategori($holding, $id_kategori)
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

        // ===== KONTEKS KATEGORI =====
        $kategori = KategoriDokumen::findOrFail($id_kategori);

        return view('admin.penerbit_dokumen.create', [
            'holding'         => $holding,
            'menus'           => $menus,
            'kategoriDokumen' => collect(), // tidak dipakai di mode ini
            'kategori'        => $kategori,
            'isFromKategori'  => true,
        ]);
    }

    /* ================= STORE ================= */
    public function store(Request $request, $holding)
    {
        $request->validate([
            'nama_penerbit'        => 'required|string|max:255',
            'jenis_penerbit'       => 'required|string|max:255',
            'kategori_dokumen_id'  => 'required|exists:kategori_dokumen_legal,id_kategori',
        ]);

        PenerbitDokumen::create([
            'nama_penerbit'       => $request->nama_penerbit,
            'jenis_penerbit'      => $request->jenis_penerbit,
            'alamat_penerbit'     => $request->alamat_penerbit,
            'nama_pic_penerbit'   => $request->nama_pic_penerbit,
            'nomor_pic_penerbit'  => $request->nomor_pic_penerbit,
            'kategori_dokumen_id' => $request->kategori_dokumen_id,
        ]);

        // ===== REDIRECT KONTEKSTUAL =====
        if ($request->filled('from_kategori') && $request->filled('redirect_kategori_id')) {
            return redirect()
                ->route(
                    'dokumen_legal.kategori.kategori_master.show',
                    [$holding, $request->redirect_kategori_id]
                )
                ->with('success', 'Penerbit dokumen berhasil ditambahkan');
        }

        // ===== DEFAULT =====
        return redirect()
            ->route('dokumen_legal.penerbit.index', $holding)
            ->with('success', 'Penerbit dokumen berhasil ditambahkan');
    }

    /* ================= SHOW ================= */
    public function show($holding, $id)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        // MENU SIDEBAR (WAJIB)
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

        $penerbitDokumen = PenerbitDokumen::with('kategoriDokumen')
            ->where('id_penerbit', $id)
            ->firstOrFail();

        return view('admin.penerbit_dokumen.show', compact(
            'holding',
            'menus',
            'penerbitDokumen'
        ));
    }


    /* ================= EDIT ================= */
    public function edit(Request $request, $holding, $id)
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

        // ===== DATA =====
        $penerbitDokumen = PenerbitDokumen::with('kategoriDokumen')
            ->where('id_penerbit', $id)
            ->firstOrFail();

        $kategoriDokumen = KategoriDokumen::orderBy('nama_kategori')->get();

        return view('admin.penerbit_dokumen.edit', [
            'holding'             => $holding,
            'menus'               => $menus,
            'penerbitDokumen'     => $penerbitDokumen,
            'kategoriDokumen'     => $kategoriDokumen,

            // konteks kategori
            'kategori'            => $penerbitDokumen->kategoriDokumen,
            'isFromKategori'      => $request->query('from_kategori') == 1,
            'redirectKategoriId'  => $request->query('redirect_kategori_id'),
        ]);
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, $holding, $id)
    {
        $request->validate([
            'nama_penerbit'        => 'required|string|max:255',
            'jenis_penerbit'       => 'required|string|max:255',
            'kategori_dokumen_id'  => 'required|exists:kategori_dokumen_legal,id_kategori',
        ]);

        $penerbitDokumen = PenerbitDokumen::where('id_penerbit', $id)->firstOrFail();

        $penerbitDokumen->update([
            'nama_penerbit'       => $request->nama_penerbit,
            'jenis_penerbit'      => $request->jenis_penerbit,
            'alamat_penerbit'     => $request->alamat_penerbit,
            'nama_pic_penerbit'   => $request->nama_pic_penerbit,
            'nomor_pic_penerbit'  => $request->nomor_pic_penerbit,
            'kategori_dokumen_id' => $request->kategori_dokumen_id,
        ]);

        // ================= REDIRECT KONTEKSTUAL =================
        if ($request->filled('from_kategori') && $request->filled('redirect_kategori_id')) {
            return redirect()
                ->route(
                    'dokumen_legal.kategori.kategori_master.show',
                    [$holding, $request->redirect_kategori_id]
                )
                ->with('success', 'Penerbit dokumen berhasil diperbarui');
        }

        // ================= DEFAULT =================
        return redirect()
            ->route('dokumen_legal.penerbit.index', $holding)
            ->with('success', 'Penerbit dokumen berhasil diperbarui');
    }

    /* ================= DESTROY ================= */
    public function destroy(Request $request, $holding, $id)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        $penerbitDokumen = PenerbitDokumen::where('id_penerbit', $id)->firstOrFail();
        $penerbitDokumen->delete();

        // ================= REDIRECT KONTEKSTUAL =================
        if ($request->filled('from_kategori') && $request->filled('redirect_kategori_id')) {
            return redirect()
                ->route(
                    'dokumen_legal.kategori.kategori_master.show',
                    [$holding->holding_code, $request->redirect_kategori_id]
                )
                ->with('success', 'Penerbit dokumen berhasil dihapus');
        }

        // ================= DEFAULT =================
        return redirect()
            ->route('dokumen_legal.penerbit.index', $holding->holding_code)
            ->with('success', 'Penerbit dokumen berhasil dihapus');
    }
}
