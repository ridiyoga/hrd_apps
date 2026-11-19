<?php

namespace App\Http\Controllers;

use App\Models\SubKategoriDokumen;
use App\Models\KategoriDokumen;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\RoleUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class SubKategoriDokumenController extends Controller
{
    /* ================= INDEX ================= */
    public function index($holding)
    {
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

        $subKategoriDokumen = SubKategoriDokumen::with('kategori')
            ->orderBy('id_sub')
            ->get();


        return view('admin.dokumen_legal.sub_kategori.index', compact(
            'holding',
            'menus',
            'subKategoriDokumen'
        ));
    }


    /* ================= CREATE (GLOBAL) ================= */
    public function create($holding, Request $request)
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

        // ===== MODE GLOBAL =====
        $kategoriDokumenList = KategoriDokumen::orderBy('nama_kategori')->get();

        return view('admin.dokumen_legal.sub_kategori.create', [
            'holding'              => $holding,
            'menus'                => $menus,
            'kategoriDokumenList'  => $kategoriDokumenList,
            'kategori'             => null,     // tidak ada konteks kategori
            'isFromKategori'       => false,    // FLAG PENTING
        ]);
    }


    /* ================= CREATE FROM KATEGORI ================= */
    public function createFromKategori($holding, $id_kategori, Request $request)
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

        // ===== AMBIL KATEGORI KONTEKSTUAL =====
        $kategori = KategoriDokumen::findOrFail($id_kategori);

        return view('admin.dokumen_legal.sub_kategori.create', [
            'holding'              => $holding,
            'menus'                => $menus,
            'kategoriDokumenList'  => collect(), // tidak dipakai
            'kategori'             => $kategori, // KONTEKS KATEGORI
            'isFromKategori'       => true,      // FLAG PENTING
        ]);
    }

    public function store(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        $validated = $request->validate([
            'id_sub'        => 'required|integer|unique:sub_kategori_dokumen_legal,id_sub',
            'sub_name'      => 'required|string|max:255',
            'kategori_id'   => 'required|integer|exists:kategori_dokumen_legal,id_kategori',
        ]);

        SubKategoriDokumen::create($validated);

        // ================= REDIRECT KONTEKSTUAL =================
        if ($request->filled('from_kategori') && $request->filled('redirect_kategori_id')) {
            return redirect()
                ->route(
                    'dokumen_legal.kategori.kategori_master.show',
                    [$holding->holding_code, $request->redirect_kategori_id]
                )
                ->with('success', 'Sub Kategori berhasil ditambahkan');
        }

        // ================= DEFAULT (GLOBAL) =================
        return redirect()
            ->route('dokumen_legal.kategori.kategori_sub.index', $holding->holding_code)
            ->with('success', 'Sub Kategori berhasil ditambahkan');
    }

    /* ================= SHOW ================= */
    public function show($holding, $id, Request $request)
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

        $subKategoriDokumen = SubKategoriDokumen::findOrFail($id);

        return view('admin.dokumen_legal.sub_kategori.show', compact(
            'holding',
            'menus',
            'subKategoriDokumen'
        ));
    }

    /* ================= EDIT ================= */
    public function edit(Request $request, $holding, $id_sub)
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

        // ===== DATA =====
        $subKategoriDokumen = SubKategoriDokumen::findOrFail($id_sub);

        //PAKAI NAMA YANG KONSISTEN DENGAN BLADE
        $KategoriDokumenList = KategoriDokumen::orderBy('nama_kategori')->get();

        return view('admin.dokumen_legal.sub_kategori.edit', [
            'holding'               => $holding,
            'menus'                 => $menus,
            'subKategoriDokumen'    => $subKategoriDokumen,
            'KategoriDokumenList'   => $KategoriDokumenList,

            // ===== CONTEXT AWARE =====
            'isFromKategori'        => $request->query('from_kategori') == 1,
            'redirectKategoriId'    => $request->query('redirect_kategori_id'),
        ]);
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, $holding, $id_sub)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        $subKategoriDokumen = SubKategoriDokumen::findOrFail($id_sub);

        $validated = $request->validate([
            'kategori_id'   => 'required|integer|exists:kategori_dokumen_legal,id_kategori',
            'sub_name'      => 'required|string|max:255',
        ]);

        $subKategoriDokumen->update([
            'kategori_id' => $validated['kategori_id'],
            'sub_name'    => $validated['sub_name'],
        ]);

        // ================= REDIRECT KONTEKSTUAL =================
        if ($request->filled('from_kategori') && $request->filled('redirect_kategori_id')) {
            return redirect()
                ->route(
                    'dokumen_legal.kategori.kategori_master.show',
                    [$holding->holding_code, $request->redirect_kategori_id]
                )
                ->with('success', 'Sub Kategori berhasil diperbarui');
        }

        return redirect()
            ->route('dokumen_legal.kategori.kategori_sub.index', $holding->holding_code)
            ->with('success', 'Sub Kategori berhasil diupdate');
    }

    /* ================= DELETE ================= */
    public function destroy(Request $request, $holding, $id)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        SubKategoriDokumen::findOrFail($id)->delete();

        // REDIRECT KONTEKSTUAL
        if ($request->filled('from_kategori') && $request->filled('redirect_kategori_id')) {
            return redirect()
                ->route(
                    'dokumen_legal.kategori.kategori_master.show',
                    [$holding->holding_code, $request->redirect_kategori_id]
                )
                ->with('success', 'Sub Kategori berhasil dihapus');
        }

        // DEFAULT
        return redirect()
            ->route('dokumen_legal.kategori.kategori_sub.index', $holding->holding_code)
            ->with('success', 'Sub Kategori berhasil dihapus');
    }
}
