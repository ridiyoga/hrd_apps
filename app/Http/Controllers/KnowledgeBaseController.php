<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Jenssegers\Agent\Agent;
use Ramsey\Uuid\Uuid;
use App\Services\TicketApprovalService;
use Illuminate\Support\Facades\Storage;

use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\User;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\KnowledgeBaseKategori;
use App\Models\KnowledgeBaseArtikel;
use App\Models\KnowledgeBaseAttachment;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KnowledgeBaseController extends Controller
{
    public function kb_index(Request $request, $holding_code)
    {
        session(['url.intended' => url()->previous()]);
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }

        $holding  = Holding::where('holding_code', $holding_code)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::id())
            ->pluck('role_menu_id')
            ->toArray();

        $roleId = count($get_role) > 0 ? $get_role : null;

        $menus = $roleId
            ? Menu::whereIn('id', function ($query) use ($roleId) {
                $query->select('menu_id')
                    ->from('role_menus')
                    ->whereIn('role_id', $roleId);
            })
            ->whereNull('parent_id')
            ->with('children')
            ->where('kategori', 'web')
            ->orderBy('sort_order')
            ->get()
            : collect();

        return view('admin.knowledge_base.index', [
            'title'   => 'Knowledge Base',
            'holding' => $holding,
            'menus'   => $menus,
        ]);
    }

    public function kb_kategori_data(Request $request)
    {
        session(['url.intended' => url()->previous()]);
        $kategori = KnowledgeBaseKategori::whereNull('kb_parent_id')
            ->with('children')
            ->orderBy('kb_sort_order')
            ->get();

        return view('admin.knowledge_base.kategori', [
            'kategori' => $kategori,
        ]);
    }

    public function kb_kategori_tree(Request $request)
    {
        $kategori = KnowledgeBaseKategori::whereNull('kb_parent_id')
            ->with('children')
            ->orderBy('kb_sort_order')
            ->get();

        return response()->json($kategori);
    }

    public function kb_kategori_data_parent(Request $request)
    {
        $kategori = KnowledgeBaseKategori::with('parent:id,kb_name')
            ->select('id', 'kb_name', 'kb_parent_id')
            ->orderBy('kb_name')
            ->get();
        return response()->json($kategori);
    }

    public function kb_kategori_store(Request $request)
    {
        session(['url.intended' => url()->previous()]);
        $validator = Validator::make($request->all(), [
            'kb_parent_id'  => 'nullable|string|exists:knowledge_base_kategori,id',
            'kb_name'       => 'required|string|max:255',
            'kb_deskripsi'  => 'required|string',
            'kb_sort_order' => 'nullable|integer',
            'kb_icon'       => 'nullable|string|max:100',
            'kb_is_active'  => 'nullable|in:0,1',
            'kb_access_type' => 'nullable|in:public,restricted',
            'departments'    => 'nullable|array',
            'departments.*'  => Rule::exists((new Departemen)->getTable(), 'id'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $nextSort = (int) KnowledgeBaseKategori::max('kb_sort_order') + 1;

            $kategori = KnowledgeBaseKategori::create([
                'kb_parent_id'   => $request->input('kb_parent_id') ?: null,
                'kb_name'        => $request->input('kb_name'),
                'kb_slug'        => Str::slug($request->input('kb_name')),
                'kb_deskripsi'   => $request->input('kb_deskripsi'),
                'kb_sort_order'  => $request->filled('kb_sort_order') ? (int) $request->input('kb_sort_order') : $nextSort,
                'kb_icon'        => $request->input('kb_icon'),
                'kb_is_active'   => $request->input('kb_is_active', 1),
                'kb_access_type' => $request->input('kb_access_type', 'public'), // <-- fixed
                'created_by'     => Auth::id(),
            ]);

            $kategori->allowedDepartments()->sync(
                $request->input('kb_access_type') === 'restricted'
                    ? $request->input('departments', [])
                    : []
            );

            DB::commit();
            return response()->json(['code' => 200, 'message' => 'Kategori berhasil ditambahkan.', 'data' => $kategori]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan kategori KB: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function kb_kategori_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kb_parent_id'   => 'nullable|string|exists:knowledge_base_kategori,id',
            'kb_name'        => 'required|string|max:255',
            'kb_deskripsi'   => 'required|string',
            'kb_sort_order'  => 'nullable|integer',
            'kb_icon'        => 'nullable|string|max:100',
            'kb_is_active'   => 'nullable|in:0,1',
            'kb_access_type' => 'nullable|in:public,restricted',
            'departments'    => 'nullable|array',
            'departments.*'  => Rule::exists((new Departemen)->getTable(), 'id'),
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $kategori = KnowledgeBaseKategori::findOrFail($id);

            if ($request->input('kb_parent_id') === $id) {
                return response()->json(['status' => 'error', 'message' => 'Kategori tidak bisa menjadi parent dirinya sendiri.'], 422);
            }

            $accessType = $request->input('kb_access_type', $kategori->kb_access_type);

            $kategori->update([
                'kb_parent_id'   => $request->input('kb_parent_id') ?: null,
                'kb_name'        => $request->input('kb_name'),
                'kb_slug'        => Str::slug($request->input('kb_name')),
                'kb_deskripsi'   => $request->input('kb_deskripsi'),
                'kb_sort_order'  => $request->filled('kb_sort_order') ? (int) $request->input('kb_sort_order') : $kategori->kb_sort_order,
                'kb_icon'        => $request->input('kb_icon'),
                'kb_is_active'   => $request->input('kb_is_active', $kategori->kb_is_active),
                'kb_access_type' => $accessType,
            ]);

            $kategori->allowedDepartments()->sync(
                $accessType === 'restricted' ? $request->input('departments', []) : []
            );

            DB::commit();
            return response()->json(['code' => 200, 'message' => 'Kategori berhasil diupdate.', 'data' => $kategori->fresh()]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate kategori KB (ID: ' . $id . '): ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengupdate kategori.'], 500);
        }
    }

    public function kb_kategori_destroy($id)
    {
        DB::beginTransaction();
        try {
            $kategori = KnowledgeBaseKategori::findOrFail($id);

            $kategori->delete();

            DB::commit();

            return response()->json([
                'code'    => 200,
                'message' => 'Kategori berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus kategori knowledge base (ID: ' . $id . '): ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menghapus kategori.',
            ], 500);
        }
    }

    public function kba_artikel_create(Request $request, $holding_code, $kategori_id, $kb_slug)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }

        $holding  = Holding::where('holding_code', $holding_code)->first();
        $kategori = KnowledgeBaseKategori::where('id', $kategori_id)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::id())
            ->pluck('role_menu_id')
            ->toArray();

        $roleId = count($get_role) > 0 ? $get_role : null;

        $kategori = KnowledgeBaseKategori::where('id', $kategori_id)->first();

        $menus = $roleId
            ? Menu::whereIn('id', function ($query) use ($roleId) {
                $query->select('menu_id')
                    ->from('role_menus')
                    ->whereIn('role_id', $roleId);
            })
            ->whereNull('parent_id')
            ->with('children')
            ->where('kategori', 'web')
            ->orderBy('sort_order')
            ->get()
            : collect();

        return view('admin.knowledge_base.new-article', [
            'title'   => 'Knowledge Base',
            'menus'   => $menus,
            'holding' => $holding,
            'kategori_id' => $kategori_id,
            'kb_slug' => $kb_slug,
            'kategori' => $kategori,
        ]);
    }

    public function kba_artikel_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kb_kategori_id' => 'required|integer|exists:knowledge_base_kategori,id',
            'judul'          => 'required|string|max:255',
            'kba_slug'       => 'required|string|max:255',
            'konten'         => 'required|string',
            'status'         => 'required|in:publik,draft',
            'tags'           => 'nullable|string|max:255',
            'attachment'     => 'nullable|array',
            'attachment.*'   => 'file|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            $artikel = KnowledgeBaseArtikel::create([
                'kba_kategori_id' => $request->kb_kategori_id,
                'kba_title'       => $request->judul,
                'kba_slug'        => $request->kba_slug,
                'kba_konten'      => $request->konten,
                'kba_status'      => $request->status,
                'kba_tags'        => $request->tags ? explode(',', $request->tags) : null,
                'created_by'      => Auth::id(),
                'published_at'    => $request->status == 'publik'
                                        ? now()
                                        : null,
            ]);

            // Save attachments
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    $path = $file->store('knowledge_base', 'public');
                    
                    KnowledgeBaseAttachment::create([
                        'id'                 => (string) Uuid::uuid4(),
                        'kba_artikel_id'     => $artikel->id,
                        'kba_file_name'      => basename($path),
                        'kba_original_name'  => $file->getClientOriginalName(),
                        'kba_file_path'      => $path,
                        'kba_file_extension' => strtolower($file->getClientOriginalExtension()),
                        'kba_file_size'      => $file->getSize(),
                        'kba_mime_type'      => $file->getMimeType(),
                        'created_by'         => Auth::id(),
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'code' => 200,
                'message' => 'Artikel berhasil ditambahkan',
                'data' => $artikel
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function kba_artikel_edit(Request $request, $holding_code, $kategori_id, $id)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }

        $holding  = Holding::where('holding_code', $holding_code)->first();
        $artikel = KnowledgeBaseArtikel::with(['attachments', 'allowedDepartments:id'])->findOrFail($id);
        $kategori = KnowledgeBaseKategori::where('id', $kategori_id)->first();

        $get_role = RoleUsers::where('role_user_id', Auth::id())
            ->pluck('role_menu_id')
            ->toArray();
        $roleId = count($get_role) > 0 ? $get_role : null;

        $menus = $roleId
            ? Menu::whereIn('id', function ($query) use ($roleId) {
                $query->select('menu_id')
                    ->from('role_menus')
                    ->whereIn('role_id', $roleId);
            })
            ->whereNull('parent_id')
            ->with('children')
            ->where('kategori', 'web')
            ->orderBy('sort_order')
            ->get()
            : collect();

        return view('admin.knowledge_base.edit-article', [
            'title'    => 'Edit Artikel',
            'menus'    => $menus,
            'holding'  => $holding,
            'artikel'  => $artikel,
            'kategori' => $kategori,
        ]);
    }

    public function kba_artikel_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'judul'          => 'required|string|max:255',
            'kba_slug'       => 'required|string|max:255',
            'konten'         => 'required|string',
            'status'         => 'required|in:publik,draft',
            'tags'           => 'nullable|string|max:255',
            'deleted_files'  => 'nullable|string',
            'attachment'     => 'nullable|array',
            'attachment.*'   => 'file|max:51200',
        ], [
            'kba_slug.regex' => 'Slug hanya boleh berisi huruf, angka, dan tanda hubung.',
            'judul.required' => 'Judul harus diisi.',
            'konten.required' => 'Konten harus diisi.',
            'status.required' => 'Status harus diisi.',
            'tags.required' => 'Tag harus diisi.',
            'deleted_files.required' => 'Deleted files harus diisi.',
            'attachment.required' => 'Attachment harus diisi.',
            'attachment.*.required' => 'Attachment harus diisi.',
            'attachment.*.file' => 'Attachment harus berupa file.',
            'attachment.*.max' => 'Attachment tidak boleh lebih dari 50MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'   => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $artikel = KnowledgeBaseArtikel::findOrFail($id);

            $artikel->update([
                'kba_title'    => $request->input('judul'),
                'kba_slug'     => $request->input('kba_slug'),
                'kba_konten'   => $request->input('konten'),
                'kba_status'   => $request->input('status'),
                'kba_tags'     => $request->input('tags') ? explode(',', $request->input('tags')) : null,
                'published_at' => $request->input('status') === 'publik'
                                    ? ($artikel->published_at ?? now())
                                    : null,
            ]);

            // Handle deleted files
            if ($request->filled('deleted_files')) {
                $deletedIds = explode(',', $request->input('deleted_files'));
                $attachmentsToDelete = KnowledgeBaseAttachment::whereIn('id', $deletedIds)
                    ->where('kba_artikel_id', $artikel->id)
                    ->get();

                foreach ($attachmentsToDelete as $attachment) {
                    if (Storage::disk('public')->exists($attachment->kba_file_path)) {
                        Storage::disk('public')->delete($attachment->kba_file_path);
                    }
                    $attachment->delete();
                }
            }

            // Save new attachments
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    $path = $file->store('knowledge_base', 'public');
                    
                    KnowledgeBaseAttachment::create([
                        'id'                 => (string) Uuid::uuid4(),
                        'kba_artikel_id'     => $artikel->id,
                        'kba_file_name'      => basename($path),
                        'kba_original_name'  => $file->getClientOriginalName(),
                        'kba_file_path'      => $path,
                        'kba_file_extension' => strtolower($file->getClientOriginalExtension()),
                        'kba_file_size'      => $file->getSize(),
                        'kba_mime_type'      => $file->getMimeType(),
                        'created_by'         => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'code'    => 200,
                'message' => 'Artikel berhasil diupdate.',
                'data'    => $artikel->fresh()->load('attachments'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate artikel KB (ID: ' . $id . '): ' . $e->getMessage());

            return response()->json([
                'code'    => 500,
                'message' => 'Terjadi kesalahan saat mengupdate artikel: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function kba_artikel_by_kategori(Request $request, $kategori_id)
    {
        $kategori = KnowledgeBaseKategori::find($kategori_id);

        if (!$kategori) {
            return response()->json([
                'code'    => 404,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        $artikel = KnowledgeBaseArtikel::where('kba_kategori_id', $kategori_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($art) {
                return [
                    'id'                => $art->id,
                    'judul'             => $art->kba_title,
                    'slug'              => $art->kba_slug,
                    'konten'            => $art->kba_konten,
                    'status'            => $art->kba_status,
                    'access_override'   => $art->kba_access_override,
                    'tags'              => is_array($art->kba_tags) ? implode(',', $art->kba_tags) : $art->kba_tags,
                    'view_count'        => $art->kba_view_count,
                    'published_at'      => $art->published_at,
                    'updated_at'        => $art->updated_at,
                    'updated_at_human'  => $art->updated_at ? $art->updated_at->diffForHumans() : null,
                ];
            });

        return response()->json([
            'code'     => 200,
            'kategori' => $kategori,
            'data'     => $artikel,
        ]);
    }

    public function kba_artikel_destroy($id)
    {
        DB::beginTransaction();
        try {
            $artikel = KnowledgeBaseArtikel::findOrFail($id);

            // Clean up files and database entries for attachments
            $attachments = $artikel->attachments;
            foreach ($attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment->kba_file_path)) {
                    Storage::disk('public')->delete($attachment->kba_file_path);
                }
                $attachment->delete();
            }

            $artikel->delete();

            DB::commit();

            return response()->json([
                'code'    => 200,
                'message' => 'Artikel berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus artikel knowledge base (ID: ' . $id . '): ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menghapus artikel.',
            ], 500);
        }
    }


    // user Mobile
    private function buildMenus(array $get_role): array
    {
        if (empty($get_role)) {
            return [collect(), collect(), collect()];
        }

        $main = RoleMenu::with(['Menu' => fn($q) => $q->where('kategori', 'mobile')])
            ->whereHas('Menu', fn($q) => $q->where('kategori', 'mobile')->where('main', 1)->orderBy('sort_order'))
            ->whereIn('role_id', $get_role)->get();

        $sub = RoleMenu::with(['Menu' => fn($q) => $q->where('kategori', 'mobile')])
            ->whereHas('Menu', fn($q) => $q->where('kategori', 'mobile')->where('main', 0)->orderBy('sort_order'))
            ->whereIn('role_id', $get_role)->get();

        $bar = RoleMenu::with(['Menu' => fn($q) => $q->where('kategori', 'mobile')])
            ->whereHas('Menu', fn($q) => $q->where('kategori', 'mobile')->where('main', 2)->orderBy('sort_order'))
            ->whereIn('role_id', $get_role)->get();

        return [$main, $sub, $bar];
    }

    private function resolveMenuId($menu_id = null)
    {
        if ($menu_id) {
            if (Auth::check()) {
                User::where('id', Auth::id())->update(['menu_active' => $menu_id]);
            }
            return $menu_id;
        }

        $active = Auth::user()?->menu_active;
        if ($active) {
            return $active;
        }

        $menu = Menu::where(function ($q) {
                $q->where('url', 'like', '%knowledge-base%')
                  ->orWhere('url', 'like', '%kb%');
            })
            ->where('kategori', 'mobile')
            ->first();

        $menuId = $menu?->id;
        if ($menuId && Auth::check()) {
            User::where('id', Auth::id())->update(['menu_active' => $menuId]);
        }
        return $menuId;
    }

    public function kb_user_index(Request $request, $menu_id = null)
    {
        session(['url.intended' => url()->previous()]);
        if (!Auth::check()){
            return redirect('/');
        }
        $menu_id       = $this->resolveMenuId($menu_id);
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $get_role      = RoleUsers::where('role_user_id', Auth::id())->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        $departmentId = $this->currentDepartmentIds();
        $kategori = KnowledgeBaseKategori::where('kb_parent_id', null)
            ->where('kb_is_active', true)
            ->withCount([
                'children',
                'artikel' => fn ($q) => $q->where('kba_status', 'publik'),
            ])
            ->orderBy('kb_sort_order')
            ->get()
            ->filter(fn ($kat) => $kat->isAccessibleByDepartment($departmentId))
            ->values();
        $kategori->each(function ($item) {
            $parts = [];
            if ($item->children_count > 0) $parts[] = "{$item->children_count} subfolder";
            if ($item->artikel_count  > 0) $parts[] = "{$item->artikel_count} artikel";
            $item->artikel_label = implode(' · ', $parts) ?: 'Kosong';
        });
        $allArtikel = KnowledgeBaseArtikel::selectRaw('kba_status, COUNT(*) as total')
            ->groupBy('kba_status')
            ->pluck('total', 'kba_status');
        $stats = [
            'total_kategori' => $kategori->count(),
            'total_artikel'  => $allArtikel->sum(),
            'total_publik'   => $allArtikel->get('publik', 0),
            'total_draft'    => $allArtikel->get('draft', 0),
        ];

        return view('users.knowledge_base.index', [
            'title'         => 'Knowledge Base',
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
            'menu_id'       => $menu_id,
            'kategori'      => $kategori,
            'stats'         => $stats,
        ]);
    }

    private function buildBreadcrumb(KnowledgeBaseKategori $kategori): array
    {
        $crumbs  = [];
        $current = $kategori;
        while ($current) {
            array_unshift($crumbs, $current);
            $current = $current->kb_parent_id
                ? KnowledgeBaseKategori::find($current->kb_parent_id)
                : null;
        }
        return $crumbs;
    }

    public function kb_user_kategori(Request $request, $kategori_slug)
    {
        session(['url.intended' => url()->previous()]);
        if (!Auth::check()){
            return redirect('/');
        }

        // Jika request berupa ID menu yang diklik dari navbar / mobile menu
        if (is_numeric($kategori_slug)) {
            $isCategory = KnowledgeBaseKategori::where('id', $kategori_slug)->orWhere('kb_slug', $kategori_slug)->exists();
            if (!$isCategory) {
                if (Auth::check()) {
                    User::where('id', Auth::id())->update(['menu_active' => (int)$kategori_slug]);
                }
                return redirect()->route('user.kb.index');
            }
        }

        $menu_id       = $this->resolveMenuId();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $get_role      = RoleUsers::where('role_user_id', Auth::id())->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        $kategori = KnowledgeBaseKategori::where('kb_slug', $kategori_slug)
            ->orWhere('id', is_numeric($kategori_slug) ? $kategori_slug : 0)
            ->firstOrFail();
        $departmentId = $this->currentDepartmentIds();

        if (! $kategori->isAccessibleByDepartment($departmentId)) {
            abort(403, 'Anda tidak memiliki akses ke kategori ini.');
        }
        $subKategori = $kategori->child_kategori()
            ->where('kb_is_active', true)
            ->withCount([
                'children',
                'artikel' => fn ($q) => $q->where('kba_status', 'publik'),
            ])
            ->get()
            ->filter(fn ($sub) => $sub->isAccessibleByDepartment($departmentId))
            ->values();
        $subKategori->each(function ($item) {
            $parts = [];
            if ($item->children_count > 0) $parts[] = "{$item->children_count} subfolder";
            if ($item->artikel_count  > 0) $parts[] = "{$item->artikel_count} artikel";
            $item->artikel_label = implode(' · ', $parts) ?: 'Kosong';
        });

        $artikels = $kategori->artikel()
            ->where('kba_status', 'publik')
            ->get()
            ->filter(fn ($art) => $art->isAccessibleByDepartment($departmentId))
            ->values();
        $breadcrumbs = $this->buildBreadcrumb($kategori);
        return view('users.knowledge_base.kategori', [
            'title'         => $kategori->kb_name,
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
            'menu_id'       => $menu_id,
            'kategori'      => $kategori,
            'subKategori'   => $subKategori,
            'artikels'      => $artikels,
            'breadcrumbs'   => $breadcrumbs,
        ]);
    }

    public function kb_user_search(Request $request)
    {
        session(['url.intended' => url()->previous()]);
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['code' => 200, 'data' => []]);
        }
        $departmentId = $this->currentDepartmentIds();
        $results = KnowledgeBaseArtikel::where('kba_status', 'publik')
            ->where(function ($query) use ($q) {
                $query->where('kba_title', 'ILIKE', "%{$q}%")
                    ->orWhere('kba_konten', 'ILIKE', "%{$q}%")
                    ->orWhereRaw('CAST(kba_tags AS TEXT) ILIKE ?', ["%{$q}%"]);
            })
            ->with('category')
            ->get()
            ->filter(fn ($art) => $art->isAccessibleByDepartment($departmentId))
            ->values();
        return response()->json([
            'code' => 200,
            'data' => $results->map(fn ($art) => [
                'id'         => $art->id,
                'kba_slug'   => $art->kba_slug,
                'kba_title'  => $art->kba_title,
                'kba_konten' => $art->kba_konten,
                'kba_status' => $art->kba_status,
                'kba_tags'   => is_array($art->kba_tags)
                    ? $art->kba_tags
                    : (is_string($art->kba_tags) ? explode(',', $art->kba_tags) : []),
                'kategori'   => $art->category?->kb_name,
            ]),
        ]);
    }

    public function kb_user_detail(Request $request, $artikel_slug)
    {
        session(['url.intended' => url()->previous()]);
        if (!Auth::check()){
            return redirect('/');
        }
        $menu_id       = $this->resolveMenuId();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $get_role      = RoleUsers::where('role_user_id', Auth::id())->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);

        $departmentIds = $this->currentDepartmentIds();

        $artikel = KnowledgeBaseArtikel::with('category')
            ->where('kba_slug', $artikel_slug)
            ->orWhere('id', is_numeric($artikel_slug) ? $artikel_slug : 0)
            ->firstOrFail();
        if (! $artikel->isAccessibleByDepartment($departmentIds)) {
            abort(403, 'Anda tidak memiliki akses ke artikel ini.');
        }   
        $kategori = KnowledgeBaseKategori::findOrFail($artikel->kba_kategori_id);

        $artikel->increment('kba_view_count');
        $tags = $artikel->kba_tags ?? [];

        $related = KnowledgeBaseArtikel::where('kba_status', 'publik')
            ->where('id', '!=', $artikel->id)
            ->where(function ($q) use ($artikel) {
                $q->where('kba_kategori_id', $artikel->kba_kategori_id);
                $q->orWhere('kba_title', 'ILIKE', '%' . $artikel->kba_title . '%');
            })
            ->get()
            ->filter(fn ($item) => $item->isAccessibleByDepartment($departmentIds))
            ->map(function ($item) use ($tags, $artikel) {
                $score = 0;
                if ($item->kba_kategori_id == $artikel->kba_kategori_id) {
                    $score += 10;
                }
                $itemTags = $item->kba_tags ?? [];
                if (!empty($tags) && !empty($itemTags)) {
                    $sameTags = array_intersect(
                        array_map('strtolower', $tags),
                        array_map('strtolower', $itemTags)
                    );
                    $score += count($sameTags) * 5;
                }
                similar_text(strtolower($artikel->kba_title), strtolower($item->kba_title), $percent);
                $score += $percent / 10;
                $item->score = $score;
                return $item;
            })
            ->sortByDesc('score')
            ->take(3)
            ->values();

        $breadcrumbs = $this->buildBreadcrumb($kategori);

        return view('users.knowledge_base.detail', [
            'title'         => $artikel->kba_title,
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
            'menu_id'       => $menu_id,
            'artikel'       => $artikel,
            'kategori'      => $kategori,
            'related'       => $related,
            'breadcrumbs'   => $breadcrumbs,
        ]);
    }

    public function kb_user_download($attachment_id)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $att     = KnowledgeBaseAttachment::findOrFail($attachment_id);
        $artikel = KnowledgeBaseArtikel::findOrFail($att->kba_artikel_id);

        // Pengecekan akses sama seperti kb_user_detail
        if (! $artikel->isAccessibleByDepartment($this->currentDepartmentIds())) {
            abort(403, 'Anda tidak memiliki akses ke lampiran ini.');
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($att->kba_file_path)) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        return $disk->download(
            $att->kba_file_path,
            $att->kba_original_name ?: $att->kba_file_name
        );
    }

    protected function currentDepartmentIds(): array
    {
        $karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        if (! $karyawan) {
            return [];
        }

        return $karyawan->KaryawanJabatan()
            ->pluck('departemen_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    public function kb_departemen_list($holding_code)
    {
        $holding = Holding::where('holding_code', $holding_code)->firstOrFail();

        return response()->json(
            Departemen::where('holding', $holding->id)
                ->orderBy('nama_departemen')
                ->get(['id', 'nama_departemen'])
        );
    }

    public function kb_kategori_show($id)
    {
        $kategori = KnowledgeBaseKategori::with('allowedDepartments:id')->findOrFail($id);
        return response()->json($kategori);
    }

    public function kb_artikel_show($id)
    {
        $artikel = KnowledgeBaseArtikel::with('allowedDepartments:id')->findOrFail($id);
        return response()->json($artikel);
    }

    public function kb_kategori_access_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kb_access_type' => 'required|in:public,restricted',
            'departments'    => 'nullable|array',
            'departments.*'  => Rule::exists((new Departemen)->getTable(), 'id'),
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
        // dd($id);
        DB::beginTransaction();
        try {
            $kategori = KnowledgeBaseKategori::findOrFail($id);
            $kategori->allowedDepartments()->detach();
            $kategori->update(['kb_access_type' => $request->kb_access_type]);
            $kategori->allowedDepartments()->sync(
                $request->kb_access_type === 'restricted' ? $request->input('departments', []) : []
            );
            DB::commit();
            return response()->json([
                'code'    => 200,
                'message' => 'Akses kategori berhasil diperbarui.',
                'data'    => $kategori->fresh('allowedDepartments'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate akses kategori KB (ID: ' . $id . '): ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(), // TEMP: reveal real error, revert before prod
            ], 500);
        }
    }

    public function kb_artikel_access_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kba_access_override' => 'required|in:inherit,public,restricted',
            'departments'         => 'nullable|array',
            'departments.*'       => Rule::exists((new Departemen)->getTable(), 'id'),
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $artikel = KnowledgeBaseArtikel::findOrFail($id);
            $artikel->allowedDepartments()->detach();
            $artikel->update(['kba_access_override' => $request->kba_access_override]);
            $artikel->allowedDepartments()->sync(
                $request->kba_access_override === 'restricted' ? $request->input('departments', []) : []
            );

            DB::commit();

            return response()->json([
                'code'    => 200,
                'message' => 'Akses artikel berhasil diperbarui.',
                'data'    => $artikel->fresh('allowedDepartments'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate akses kategori KB (ID: ' . $id . '): ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

