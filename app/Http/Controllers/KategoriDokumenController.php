<?php

namespace App\Http\Controllers;

use App\Models\KategoriDokumen;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\RoleUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriDokumenController extends Controller
{
    /* ================= INDEX ================= */
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        // ===== MENU SIDEBAR (WAJIB) =====
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

        $kategoriDokumen = KategoriDokumen::orderBy('id_kategori')->get();

        return view('admin.dokumen_legal.kategori.index', compact(
            'holding',
            'menus',
            'kategoriDokumen'
        ));
    }


    /* ================= CREATE ================= */
    public function create($holding)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

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

        return view('admin.dokumen_legal.kategori.create', compact(
            'holding',
            'menus'
        ));
    }

    /* ================= STORE ================= */
    public function store(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        $validated = $request->validate([
            'id_kategori'   => 'required|integer|unique:kategori_dokumen_legal,id_kategori',
            'nama_kategori' => 'required|string|max:255',
            'reminder_days' => 'nullable|integer',
        ]);

        KategoriDokumen::create($validated);

        return redirect()
            ->route('dokumen_legal.kategori.kategori_master.index', $holding->holding_code)
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /* ================= SHOW ================= */
    public function show($holding, $id, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
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

        // ===== DATA KATEGORI + RELASI =====
        $kategoriDokumen = KategoriDokumen::with([
            'subKategori',
            'penerbitDokumen',
        ])
            ->where('id_kategori', $id)
            ->firstOrFail();

        return view('admin.dokumen_legal.kategori.show', compact(
            'holding',
            'menus',
            'kategoriDokumen'
        ));
    }

    /* ================= EDIT ================= */
    public function edit($holding, $id_kategori, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->firstOrFail();
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

        $kategoriDokumen = KategoriDokumen::findOrFail($id_kategori);

        return view('admin.dokumen_legal.kategori.edit', compact(
            'holding',
            'menus',
            'kategoriDokumen'
        ));
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, $holding, $id_kategori)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();
        $kategoriDokumen = KategoriDokumen::findOrFail($id_kategori);

        $validated = $request->validate([
            'id_kategori'   => 'required|integer|unique:kategori_dokumen_legal,id_kategori,' . $id_kategori . ',id_kategori',
            'nama_kategori' => 'required|string|max:255',
            'reminder_days' => 'nullable|integer',
        ]);

        $kategoriDokumen->update($validated);

        return redirect()
            ->route('dokumen_legal.kategori.kategori_master.index', $holding->holding_code)
            ->with('success', 'Kategori berhasil diupdate');
    }

    /* ================= DELETE ================= */
    public function destroy($holding, $id)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        KategoriDokumen::findOrFail($id)->delete();

        return redirect()
            ->route('dokumen_legal.kategori.kategori_master.index', $holding->holding_code)
            ->with('success', 'Kategori berhasil dihapus');
    }
}
