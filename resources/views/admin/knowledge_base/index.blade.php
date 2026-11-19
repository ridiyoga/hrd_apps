@extends('admin.layouts.dashboard')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style type="text/css">
.my-swal { z-index: 9999; }

    /* ── breadcrumb kategori ── */
    .kb-breadcrumb {
        font-size: 12px;
        color: #888;
        margin-bottom: 2px;
    }

    /* ── status badge pill ── */
    .badge-publik  { background:#d1fae5; color:#065f46; }
    .badge-draft   { background:#fef3c7; color:#92400e; }

    /* ── card artikel di right panel ── */
    .artikel-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: box-shadow .15s;
    }
    .artikel-card:hover { box-shadow: 0 2px 10px rgba(124,58,237,.12); }
    .artikel-card .artikel-title {
        font-weight: 600;
        font-size: 14px;
        color: #1f2937;
        margin-bottom: 4px;
    }
    .artikel-card .artikel-meta {
        font-size: 11px;
        color: #9ca3af;
    }
    .artikel-card .artikel-actions {
        display: flex;
        gap: 6px;
        margin-top: 10px;
    }

    /* ── tree kategori ── */
    .kb-tree { list-style: none; padding: 0; margin: 0; }
    .kb-tree li { margin: 2px 0; }
    .kb-tree .tree-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 10px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        color: #374151;
        transition: background .15s;
        user-select: none;
    }
    .kb-tree .tree-item:hover  { background: #f0eeff; color: #6d28d9; }
    .kb-tree .tree-item.active { background: #7c3aed; color: #fff; }
    .kb-tree .tree-item.active i { color: #fff; }
    .kb-tree .tree-item i { color: #7c3aed; font-size: 12px; min-width: 14px; }

    /* indent child — satu rule, semua level */
    .kb-tree li > ul {
        list-style: none;
        padding-left: 16px;
        margin: 2px 0;
        border-left: 2px solid #ede9fe;
    }

    /* ── ikon aksi edit/hapus di tree — muncul saat hover/active ── */
    .tree-actions .btn_edit_kategori,
    .tree-actions .btn_hapus_kategori,
    .tree-actions .btn_akses_kategori {
        opacity: 0;
        transition: opacity .15s;
    }
    .kb-tree .tree-item:hover .tree-actions .btn_edit_kategori,
    .kb-tree .tree-item:hover .tree-actions .btn_hapus_kategori,
    .kb-tree .tree-item:hover .tree-actions .btn_akses_kategori {
        opacity: 1;
    }
    .kb-tree .tree-item.active .tree-actions .btn_edit_kategori {
        opacity: 1;
        color: #c4b5fd !important;
    }
    .kb-tree .tree-item.active .tree-actions .btn_hapus_kategori {
        opacity: 1;
        color: #fca5a5 !important;
    }
    .kb-tree .tree-item.active .tree-actions .btn_akses_kategori {
        opacity: 1;
        color: #00aeff !important;
    }

    /* ── chevron toggle warna saat active ── */
    .kb-tree .tree-item.active .tree-toggle {
        color: #fff !important;
    }

    /* ── folder icon warna saat hover ── */
    .kb-tree .tree-item:hover .tree-folder-icon {
        color: #6d28d9;
    }

    /* ── collapse toggle arrow ── */
    .tree-toggle {
        font-size: 10px;
        color: #9ca3af;
        cursor: pointer;
        transition: transform .15s;
        flex-shrink: 0;
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #9ca3af;
    }

    .empty-state p {
        margin-bottom: 12px;
        font-size: 14px;
    }

    /* ── right panel header ── */
    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f3f4f6;
    }
    .panel-header h5 { margin: 0; font-weight: 700; color: #1f2937; }

    /* ── modal artikel editor ── */
    #art_konten {
        min-height: 180px;
        font-family: monospace;
        font-size: 13px;
        resize: vertical;
    }

    /* ── status toggle artikel ── */
    .status-toggle {
        display: flex;
        gap: 8px;
    }
    .status-toggle label {
        flex: 1;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 8px 12px;
        text-align: center;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all .15s;
    }
    .status-toggle input[type="radio"] { display: none; }
    .status-toggle input:checked + label.for-publik { border-color: #059669; background:#d1fae5; color:#065f46; }
    .status-toggle input:checked + label.for-draft  { border-color: #d97706; background:#fef3c7; color:#92400e; }

    .empty-action-btn {
        font-size: 12px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .empty-action-btn i {
        font-size: 11px;
    }

    .swal2-container {
        z-index: 999999 !important;
    }
</style>
@endsection

@section('isi')
@include('sweetalert::alert')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">
                                <i class="fa-solid fa-book-open me-2 text-primary"></i>
                                Knowledge Base
                            </h4>
                            <small class="text-muted" id="kb_breadcrumb_top">
                                Pilih kategori untuk melihat artikel
                            </small>
                        </div>
                        <button class="btn btn-primary btn-sm btn_tambah_artikel" id="btn_tambah_artikel" style="display:none;">
                            <i class="fa-solid fa-plus me-1"></i>
                            Tambah Artikel
                        </button>
                    </div>

                    <div class="row g-0">
                        <div class="col-md-3 pe-3" style="border-right: 1px solid #f0f0f0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0" style="color: #6d46da">Kategori</h6>
                                <button class="btn btn-sm btn-outline-primary" id="btn_tambah_kategori" style="font-size: 12px;">
                                    <i class="fa-solid fa-plus me-1"></i>
                                    Tambah
                                </button>
                            </div>
                            <div id="kb_tree_wrap">
                                <div class="text-center text-muted py-3" style="font-size:13px">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                    Memuat kategori...
                                </div>
                            </div>
                        </div>

                        <div class="col-md-9 ps-4" id="kb_right_panel">
                            <div class="empty-state">
                                <i class="fa-solid fa-folder-open text-muted"></i>
                                <p class="mb-0">Pilih kategori di sebelah kiri <br>untuk melihat daftar artikel.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <div class="modal fade" id="modal_kategori" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_kategori_title">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="kat_id_edit">
                <div class="row g-3">
                    <div class="col md-8">
                        <label class="form-label fw-semibold">Nama Kategori</label>
                        <input type="text" class="form-control" id="kat_name" placeholder="Masukkan Nama Kategori" required>
                    </div>
    
                    <div class="col md-4">
                        <label class="form-label fw-semibold">Icon</label>
                        <div class="input-group">
                            <span class="input-group-text" style="cursor:pointer;">
                                <i class="fa-solid fa-icons" id="kat_icon_preview"></i>
                            </span>
                            <input type="text" class="form-control" id="kat_icon" placeholder="fa-solid fa-book" required>
                        </div>
                    </div>
    
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <input type="text" class="form-control" id="kat_deskripsi" placeholder="Masukkan Deskripsi" required>
                    </div>
    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Parent Kategori</label>
                        <select name="" id="kat_parent_id" class="form-select">
                            <option value="">-- Pilih Parent Kategori --</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Urutan</label>
                        <input type="text" class="form-control" id="kat_sort_order" placeholder="Masukkan Urutan" required>
                    </div>
    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="kat_is_active">
                            <option value="">-- Pilih Status --</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Level Akses</label>
                        <select class="form-select" id="kat_access_type">
                            <option value="public">Publik (semua karyawan)</option>
                            <option value="restricted">Dibatasi per Departemen</option>
                        </select>
                    </div>
                    <div class="col-md-12" id="kat_departments_wrap" style="display:none;">
                        <label class="form-label fw-semibold">
                            Departemen yang Diizinkan
                            <small class="text-muted fw-normal">(berlaku juga untuk semua subfolder & artikel di dalamnya, kecuali di-override)</small>
                        </label>
                        <div id="kat_departments_list" class="d-flex flex-wrap gap-2" style="max-height:160px; overflow-y:auto; border:1px solid #e5e7eb; border-radius:8px; padding:10px;">
                            <span class="text-muted" style="font-size:12px;">Memuat departemen...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn_simpan_kategori">Simpan</button>
            </div>
        </div>
    </div>
 </div>

 <div class="modal fade" id="modal_akses_kategori" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kelola Akses: <span id="akses_kategori_nama"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="akses_kat_id">
                <label class="form-label fw-semibold">Level Akses</label>
                <select class="form-select mb-3" id="akses_kat_access_type">
                    <option value="public">Publik (semua karyawan)</option>
                    <option value="restricted">Dibatasi per Departemen</option>
                </select>
                <div id="akses_kat_departments_wrap" style="display:none;">
                    <label class="form-label fw-semibold">Departemen yang Diizinkan</label>
                    <div id="akses_kat_departments_list" class="d-flex flex-wrap gap-2"
                        style="max-height:200px; overflow-y:auto; border:1px solid #e5e7eb; border-radius:8px; padding:10px;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn_simpan_akses_kategori">Simpan Akses</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_akses_artikel" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kelola Akses: <span id="akses_artikel_judul"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="akses_art_id">
                <label class="form-label fw-semibold">Level Akses</label>
                <select class="form-select mb-3" id="akses_art_access_type">
                    <option value="inherit">Ikut Kategori (default)</option>
                    <option value="public">Publik untuk Semua</option>
                    <option value="restricted">Dibatasi per Departemen</option>
                </select>
                <div id="akses_art_departments_wrap" style="display:none;">
                    <label class="form-label fw-semibold">Departemen yang Diizinkan</label>
                    <div id="akses_art_departments_list" class="d-flex flex-wrap gap-2"
                        style="max-height:200px; overflow-y:auto; border:1px solid #e5e7eb; border-radius:8px; padding:10px;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn_simpan_akses_artikel">Simpan Akses</button>
            </div>
        </div>
    </div>
</div>

 <!-- Konfirmasi hapus -->
<div class="modal fade" id="modal_hapus_konfirmasi">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger" id="modal_hapus_konfirmasi_title">
                    <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                    Hapus Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2" id="hapus_konfirmasi_text">
                Data ini akan dihapus permanen dan tidak dapat dikembalikan.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="btn_konfirm_hapus">
                    <i class="fa-solid fa-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>
    const TOKEN = "{{ csrf_token() }}";
    const BASE_KB = "{{ url('')}}";
    const holdingCode = "{{ $holding->holding_code }}";
    
    function swalLoading(text = 'Memproses...') {
        Swal.fire({
            title: text,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });
    }

    let activeKategoriId = null;
    let activeKategoriNama = '';
    let hapusCallback = null;
    let activeKategoriSlug = '';
    function loadKategoriTree() {
        $('#kb_tree_wrap').html(
            '<div class="text-center text-muted py-3" style="font-size:13px;">' +
            '<i class="fa-solid fa-spinner fa-spin me-1"></i> Memuat Kategori</div>'
        );
        $.ajax({
            url: "{{ route('knowledge_base.kategori_tree') }}",
            type: 'GET',
            success: function (res) {
                if (!res || res.length === 0) {
                    $('#kb_tree_wrap').html(
                        '<div class="empty-state">' +
                        '<i class="fa-regular fa-folder text-muted" style="font-size:40px"></i>' +
                        '<p>Belum ada kategori.</p></div>'
                    );
                    return;
                }
                $('#kb_tree_wrap').html(buildTree(res, 0));
            },
            error: function (xhr) {
                $('#kb_tree_wrap').html(
                    '<div class="text-center text-danger py-3" style="font-size:13px;">' +
                    '<i class="fa-solid fa-triangle-exclamation me-1"></i> Gagal memuat kategori</div>'
                );
            }
        });
    }
    function buildTree(items, depth) {
        if (!items || items.length === 0) return '';
        let html = depth === 0
            ? `<ul class="kb-tree">`
            : `<ul style="display:none;">`;
        items.forEach(item => {
            const hasChildren = item.children && item.children.length > 0;
            const icon = item.kb_icon || (depth === 0 ? 'fa-solid fa-folder' : 'fa-solid fa-file-lines');
            html += `<li>
                <div class="tree-item" data-id="${item.id}" data-nama="${item.kb_name}" data-slug="${item.kb_slug}">
                    <span class="tree-toggle-wrap" style="width:16px;display:inline-flex;justify-content:center;flex-shrink:0;">
                        ${hasChildren
                            ? '<i class="fa-solid fa-chevron-right tree-toggle" style="font-size:10px;color:#9ca3af;cursor:pointer;"></i>'
                            : ''}
                    </span>
                    <i class="${icon} tree-folder-icon" style="font-size:13px;"></i>
                    <span class="tree-label" style="flex:1;">${item.kb_name}</span>
                    <span class="tree-actions ms-auto" style="display:flex;gap:6px;flex-shrink:0;">
                        <i class="fa-solid fa-pen btn_edit_kategori"
                            style="font-size:11px;cursor:pointer;color:#6d46da;"
                            data-id="${item.id}"
                            data-nama="${item.kb_name}"
                            data-deskripsi="${item.kb_deskripsi || ''}"
                            data-icon="${item.kb_icon || ''}"
                            data-sort="${item.kb_sort_order || 0}"
                            data-active="${item.kb_is_active || 0}"
                            data-parent="${item.kb_parent_id || ''}"
                            data-slug="${item.kb_slug || ''}">
                        </i>
                        <i class="fa-solid fa-trash btn_hapus_kategori"
                            style="font-size:11px;cursor:pointer;color:#ef4444;"
                            data-id="${item.id}"
                            data-nama="${item.kb_name}">
                        </i>
                        <i class="fa-solid fa-shield-halved btn_akses_kategori"
                            style="font-size:11px;cursor:pointer;color:#0891b2;"
                            data-id="${item.id}"
                            data-nama="${item.kb_name}"
                            data-access="${item.kb_access_type || 'public'}">
                        </i>
                    </span>
                </div>`;
            if (hasChildren) {
                html += buildTree(item.children, depth + 1);
            }
            html += `</li>`;
        });
        html += '</ul>';
        return html;
    }
    $(document).on('click', '.tree-toggle', function (e) {
        e.stopPropagation();
        const $children = $(this).closest('li').children('ul');
        const isOpen    = $children.is(':visible');
        $children.slideToggle(150);
        $(this)
            .toggleClass('fa-chevron-right', isOpen)
            .toggleClass('fa-chevron-down', !isOpen);
    });
    $(document).on('click', '.tree-item', function (e) {
        if ($(e.target).hasClass('tree-toggle') ||
            $(e.target).hasClass('btn_edit_kategori') ||
            $(e.target).hasClass('btn_hapus_kategori') ||
            $(e.target).hasClass('btn_akses_kategori')) {
            return;
        }

        $('.tree-item').removeClass('active');
        $(this).addClass('active');
        activeKategoriId   = $(this).data('id');
        activeKategoriNama = $(this).data('nama');
        activeKategoriSlug = $(this).data('slug');
        console.log({
            id: activeKategoriId,
            nama: activeKategoriNama,
            slug: activeKategoriSlug
        })
        $('#kb_breadcrumb_top').text('Knowledge Base / ' + activeKategoriNama);
        $('#btn_tambah_artikel').show();
        loadArtikelPanel(activeKategoriId, activeKategoriNama);
    });

    function loadParentOptions(excludeId = null) {
        $.ajax({
            url: "{{ route('knowledge_base.kategori_parent') }}",
            type: 'GET',
            success: function(res) {
                let opt = '<option value="">-- Tidak Ada --</option>';
                (res || []).forEach(item => {
                    if (excludeId && item.id == excludeId) {
                        return;
                    }
                    const label = item.parent
                        ? `${item.kb_name} ⇒ ${item.parent.kb_name}`
                        : item.kb_name;
                    opt += `<option value="${item.id}">${label}</option>`;
                });
                $('#kat_parent_id').html(opt);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                $('#kat_parent_id').html('<option value="">Gagal memuat kategori</option>');
            }
        });
    }

    let departemenListCache = null;
    function loadDepartemenOptions(selectedIds = []) {
        const render = (list) => {
            let html = '';
            list.forEach(d => {
                const checked = selectedIds.includes(d.id) ? 'checked' : '';
                html += `
                    <label style="font-size:12px; display:flex; align-items:center; gap:5px; background:#f5f3ff; padding:4px 10px; border-radius:20px; cursor:pointer;">
                        <input type="checkbox" class="kat_dept_checkbox" value="${d.id}" ${checked}>
                        ${d.nama_departemen} (${d.Holding?.holding_name ?? '-'})
                    </label>`;
            });
            $('#kat_departments_list').html(html || '<span class="text-muted" style="font-size:12px;">Belum ada data departemen.</span>');
        };
        if (departemenListCache) {
            render(departemenListCache);
            return;
        }
        $.get("{{ route('knowledge_base.departemen', $holding->holding_code) }}", function (res) {
            departemenListCache = res || [];
            render(departemenListCache);
        }).fail(function () {
            $('#kat_departments_list').html('<span class="text-danger" style="font-size:12px;">Gagal memuat departemen.</span>');
        });
    }
    $('#kat_access_type').on('change', function () {
        const isRestricted = $(this).val() === 'restricted';
        $('#kat_departments_wrap').toggle(isRestricted);
        if (isRestricted) loadDepartemenOptions();
    });

    function resetKategoriModal(){
        $('#kat_id_edit').val('');
        $('#kat_name, #kat_deskripsi').val('');
        $('#kat_icon').val('fa-solid fa-folder').trigger('input');
        $('#kat_sort_order').val(0);
        $('#kat_is_active').val('1');
        $('#kat_access_type').val('public');
        $('#kat_departments_wrap').hide();
        $('#kat_departments_list').html('<span class="text-muted" style="font-size:12px;">Memuat departemen...</span>');
    }

    $('#btn_tambah_kategori').on('click', function(){
        $('#modal_kategori_title').text('Tambah Kategori');
        resetKategoriModal();
        loadParentOptions();
        $('#modal_kategori').modal('show');
    });

    $(document).on('input', '#kat_icon', function(){
        const cls = $(this).val().trim() || 'fa-solid fa-folder';
        $('#kat_icon_preview').attr('class', cls);
    })

    $('#btn_simpan_kategori').on('click', function() {
        
        const id     = $('#kat_id_edit').val();
        const name   = $('#kat_name').val().trim();
        const desk   = $('#kat_deskripsi').val().trim();
        const accessType   = $('#kat_access_type').val();
        const departments  = accessType === 'restricted'
            ? $('.kat_dept_checkbox:checked').map(function() { return $(this).val(); }).get()
            : [];

        if (!name) {
            Swal.fire('Perhatian', 'Nama kategori wajib diisi.', 'warning');
            return;
        }

        swalLoading();
        const url = id
            ? "{{ route('knowledge_base.kategori.update', ':id') }}".replace(':id', id)
            : "{{ route('knowledge_base.kategori.store') }}";

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token:        TOKEN,
                _method:       'POST',
                kb_name:       name,
                kb_deskripsi:  desk,
                kb_icon:       $('#kat_icon').val().trim(),
                kb_parent_id:  $('#kat_parent_id').val() || null,
                kb_sort_order: $('#kat_sort_order').val(),
                kb_is_active:  $('#kat_is_active').val(),
                kb_access_type: accessType,
                departments:    departments,
            },
            success: function(res) {
                Swal.close();
                if (res.code === 200) {
                    $('#modal_kategori').modal('hide');
                    loadKategoriTree();
                    Swal.fire({ icon:'success', title:'Berhasil!', text: res.message,
                        timer:2000, showConfirmButton:false });
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                }
            },
            error: function(e) {
                Swal.close();
                const msg = e.responseJSON?.message || 'Terjadi kesalahan server.';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    $(document).on('click', '.btn_edit_kategori', function() {
        const $b = $(this);
        const id = $b.data('id');

        $('#modal_kategori_title').text('Edit Kategori');
        $('#kat_id_edit').val(id);
        $('#kat_name').val($b.data('nama'));
        $('#kat_deskripsi').val($b.data('deskripsi'));
        $('#kat_icon').val($b.data('icon') || 'fa-solid fa-folder').trigger('input');
        $('#kat_sort_order').val($b.data('sort') || 0);
        $('#kat_is_active').val($b.data('active') == 1 ? '1' : '0');
        loadParentOptions(id);
        $('#kat_parent_id').val($b.data('parent') || '');
        $('#modal_kategori').modal('show');

        $.get("{{ route('knowledge_base.kategori.show', ':id') }}".replace(':id', id), function (res) {
            const accessType   = res.kb_access_type || 'public';
            const selectedIds  = (res.allowed_departments || []).map(d => d.id);

            $('#kat_access_type').val(accessType);
            $('#kat_departments_wrap').toggle(accessType === 'restricted');
            if (accessType === 'restricted') loadDepartemenOptions(selectedIds);
        });
    });

    $(document).on('click', '.btn_hapus_kategori', function() {
        const id   = $(this).data('id');
        const nama = $(this).data('nama');
        showHapusModal('Hapus kategori "' + nama + '"? Semua artikel di dalamnya juga akan terhapus.', function() {
            swalLoading();
            $.ajax({
                url: "{{ route('knowledge_base.kategori.destroy', ':id') }}".replace(':id', id),
                type: 'POST',
                data: { _token: TOKEN, _method: 'DELETE' },
                success: function(res) {
                    Swal.close();
                    $('#modal_hapus_konfirmasi').modal('hide');
                    loadKategoriTree();
                    if (activeKategoriId === id) {
                        activeKategoriId = null;
                        $('#kb_right_panel').html(
                            '<div class="empty-state"><i class="fa-regular fa-folder-open text-muted"></i>' +
                            '<p>Pilih kategori di sebelah kiri.</p></div>'
                        );
                        $('#btn_tambah_artikel').hide();
                    }
                    Swal.fire({ icon:'success', title:'Terhapus!', timer:1800, showConfirmButton:false });
                },
                error: function(e) {
                    Swal.close();
                    Swal.fire('Gagal', e.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                }
            });
        });
    });

    $(document).on('click', '.btn_akses_kategori', function () {
        const id     = $(this).data('id');
        const nama   = $(this).data('nama');
        const access = $(this).data('access') || 'public';

        $('#akses_kat_id').val(id);
        $('#akses_kategori_nama').text(nama);
        $('#akses_kat_access_type').val(access);
        $('#akses_kat_departments_wrap').toggle(access === 'restricted');

        $.get("{{ route('knowledge_base.kategori.show', ':id') }}".replace(':id', id), function (res) {
            const selectedIds = (res.allowed_departments || []).map(d => d.id);
            if (access === 'restricted') loadAksesKatDepartments(selectedIds);
        });

        $('#modal_akses_kategori').modal('show');
    });

    $('#akses_kat_access_type').on('change', function () {
        const isRestricted = $(this).val() === 'restricted';
        $('#akses_kat_departments_wrap').toggle(isRestricted);
        if (isRestricted) loadAksesKatDepartments();
    });

    let aksesDeptCache = null;
    function loadAksesKatDepartments(selectedIds = []) {
        const render = (list) => {
            let html = '';
            list.forEach(d => {
                const checked = selectedIds.includes(d.id) ? 'checked' : '';
                html += `
                    <label style="font-size:12px; display:flex; align-items:center; gap:5px; background:#f5f3ff; padding:4px 10px; border-radius:20px; cursor:pointer;">
                        <input type="checkbox" class="akses_kat_dept_checkbox" value="${d.id}" ${checked}>
                        ${d.nama_departemen}
                    </label>`;
            });
            $('#akses_kat_departments_list').html(html || '<span class="text-muted" style="font-size:12px;">Belum ada data departemen.</span>');
        };
        if (aksesDeptCache) { render(aksesDeptCache); return; }
        $.get("{{ route('knowledge_base.departemen', $holding->holding_code) }}", function (res) {
            aksesDeptCache = res || [];
            render(aksesDeptCache);
        });
    }

    $('#btn_simpan_akses_kategori').on('click', function () {
        const id     = $('#akses_kat_id').val();
        const access = $('#akses_kat_access_type').val();
        const departments = access === 'restricted'
            ? $('.akses_kat_dept_checkbox:checked').map(function () { return $(this).val(); }).get()
            : [];

        swalLoading('Menyimpan akses...');
        $.ajax({
            url: "{{ route('knowledge_base.kategori.access', ':id') }}".replace(':id', id),
            type: 'POST',
            data: { _token: TOKEN, kb_access_type: access, departments: departments },
            success: function (res) {
                Swal.close();
                $('#modal_akses_kategori').modal('hide');
                loadKategoriTree();
                Swal.fire({ icon: 'success', title: 'Akses diperbarui!', timer: 1500, showConfirmButton: false });
            },
            error: function (e) {
                Swal.close();
                Swal.fire('Gagal', e.responseJSON?.message || 'Terjadi kesalahan.', 'error');
            }
        });
    });

    $(document).on('click', '.btn_akses_artikel', function () {
        const id     = $(this).data('id');
        const judul  = $(this).data('judul');
        const access = $(this).data('access') || 'inherit';

        $('#akses_art_id').val(id);
        $('#akses_artikel_judul').text(judul);
        $('#akses_art_access_type').val(access);
        $('#akses_art_departments_wrap').toggle(access === 'restricted');

        $.get("{{ route('knowledge_base.artikel.show', ':id') }}".replace(':id', id), function (res) {
            const selectedIds = (res.allowed_departments || []).map(d => d.id);
            if (access === 'restricted') loadAksesArtDepartments(selectedIds);
        });

        $('#modal_akses_artikel').modal('show');
    });

    $('#akses_art_access_type').on('change', function () {
        const isRestricted = $(this).val() === 'restricted';
        $('#akses_art_departments_wrap').toggle(isRestricted);
        if (isRestricted) loadAksesArtDepartments();
    });

    let aksesArtDeptCache = null;
    function loadAksesArtDepartments(selectedIds = []) {
        const render = (list) => {
            let html = '';
            list.forEach(d => {
                const checked = selectedIds.includes(d.id) ? 'checked' : '';
                html += `
                    <label style="font-size:12px; display:flex; align-items:center; gap:5px; background:#f5f3ff; padding:4px 10px; border-radius:20px; cursor:pointer;">
                        <input type="checkbox" class="akses_art_dept_checkbox" value="${d.id}" ${checked}>
                        ${d.nama_departemen}
                    </label>`;
            });
            $('#akses_art_departments_list').html(html || '<span class="text-muted" style="font-size:12px;">Belum ada data departemen.</span>');
        };
        if (aksesArtDeptCache) { render(aksesArtDeptCache); return; }
        $.get("{{ route('knowledge_base.departemen', $holding->holding_code) }}", function (res) {
            aksesArtDeptCache = res || [];
            render(aksesArtDeptCache);
        });
    }

    $('#btn_simpan_akses_artikel').on('click', function () {
        const id     = $('#akses_art_id').val();
        const access = $('#akses_art_access_type').val();
        const departments = access === 'restricted'
            ? $('.akses_art_dept_checkbox:checked').map(function () { return $(this).val(); }).get()
            : [];

        swalLoading('Menyimpan akses...');
        $.ajax({
            url: "{{ route('knowledge_base.artikel.access', ':id') }}".replace(':id', id),
            type: 'POST',
            data: { _token: TOKEN, kba_access_override: access, departments: departments },
            success: function (res) {
                Swal.close();
                $('#modal_akses_artikel').modal('hide');
                loadArtikelPanel(activeKategoriId, activeKategoriNama);
                Swal.fire({ icon: 'success', title: 'Akses diperbarui!', timer: 1500, showConfirmButton: false });
            },
            error: function (e) {
                Swal.close();
                Swal.fire('Gagal', e.responseJSON?.message || 'Terjadi kesalahan.', 'error');
            }
        });
    });

    $('#modal_kategori').on('hidden.bs.modal', function() {
        resetKategoriModal();
    });

    function loadArtikelPanel(kategoriId, kategoriNama) {
        $('#kb_right_panel').html(
            '<div class="text-center text-muted py-4">' +
            '<i class="fa-solid fa-spinner fa-spin me-1"></i> Memuat artikel...</div>'
        );

        $.ajax({
            url: "{{ route('knowledge_base.artikel.by_kategori', ':id') }}".replace(':id', kategoriId),
            type: 'GET',
            success: function(res) {
                const items = res.data || [];

                let html = `
                <div class="panel-header">
                    <div>
                        <h5>${kategoriNama}</h5>
                        <small class="text-muted">${items.length} artikel</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary btn_edit_kategori"
                            data-id="${res.kategori?.id || ''}"
                            data-nama="${res.kategori?.kb_name || ''}"
                            data-deskripsi="${res.kategori?.kb_deskripsi || ''}"
                            data-icon="${res.kategori?.kb_icon || ''}"
                            data-sort="${res.kategori?.kb_sort_order || 0}"
                            data-active="${res.kategori?.kb_is_active || 0}"
                            data-parent="${res.kategori?.kb_parent_id || ''}">
                            <i class="fa-solid fa-pen me-1"></i>Edit Kategori
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn_hapus_kategori"
                            data-id="${res.kategori?.id || ''}"
                            data-nama="${res.kategori?.kb_name || ''}">
                            <i class="fa-solid fa-trash me-1"></i>
                        </button>
                    </div>
                </div>`;

                if (items.length === 0) {
                    html += `
                    <div class="empty-state">
                        <i class="fa-regular fa-file text-muted fs-1"></i>
                        <p class="mt-3 mb-2 text-muted">
                            Belum ada artikel di kategori ini.
                        </p>
                        <button class="btn btn-outline-primary btn-sm empty-action-btn btn_tambah_artikel">
                            <i class="fa-solid fa-plus"></i>
                            Tambah Artikel Pertama
                        </button>
                    </div>`;
                } else {
                    html += '<div id="artikel_list">';
                    items.forEach(function(art) {
                        const badgeClass = art.status === 'publik' ? 'badge-publik' : 'badge-draft';
                        const badgeLabel = art.status === 'publik' ? 'Publik' : 'Draft';
                        const badgeIcon  = art.status === 'publik'
                            ? '<i class="fa-solid fa-globe me-1"></i>'
                            : '<i class="fa-solid fa-pencil me-1"></i>';

                        html += `
                        <div class="artikel-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="artikel-title">${art.judul}</div>
                                <span class="badge ${badgeClass}" style="font-size:11px;border-radius:20px;padding:4px 10px;">
                                    ${badgeIcon}${badgeLabel}
                                </span>
                            </div>
                            <div class="artikel-meta">
                                <i class="fa-regular fa-clock me-1"></i>
                                ${art.updated_at_human || art.updated_at || '—'}
                                ${art.tags ? '&nbsp;·&nbsp;<i class="fa-solid fa-tag me-1"></i>' + art.tags : ''}
                            </div>
                            <div class="artikel-actions">
                                <button class="btn btn-sm btn-outline-primary btn_edit_artikel"
                                    data-kategori-id="${res.kategori?.id || ''}"
                                    data-kategori-slug="${res.kategori?.kb_slug || ''}"
                                    data-id="${art.id}"
                                    data-judul="${art.judul}"
                                    data-konten="${encodeURIComponent(art.konten || '')}"
                                    data-status="${art.status}"
                                    data-sort="${art.sort_order || 0}"
                                    data-tags="${art.tags || ''}">
                                    <i class="fa-solid fa-pen me-1"></i>Edit
                                </button>
                                <button class="btn btn-sm btn-outline-info btn_akses_artikel"
                                    data-id="${art.id}"
                                    data-judul="${art.judul}"
                                    data-access="${art.access_override || 'inherit'}">
                                    <i class="fa-solid fa-shield-halved me-1"></i>Akses
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn_hapus_artikel"
                                    data-id="${art.id}"
                                    data-judul="${art.judul}">
                                    <i class="fa-solid fa-trash me-1"></i>Hapus
                                </button>
                            </div>
                        </div>`;
                    });
                    html += '</div>';
                }

                $('#kb_right_panel').html(html);
            },
            error: function() {
                $('#kb_right_panel').html(
                    '<div class="empty-state text-danger">' +
                    '<i class="fa-solid fa-circle-exclamation"></i>' +
                    '<p>Gagal memuat artikel.</p></div>'
                );
            }
        });
    }

    $(document).on('click', '.btn_tambah_artikel', function () {
        if (!activeKategoriId) {
            Swal.fire('Peringatan', 'Silakan pilih kategori terlebih dahulu.', 'warning');
            return;
        }
        window.location.href = `${BASE_KB}/knowledge_base/${holdingCode}/artikel/create/${activeKategoriId}/${activeKategoriSlug}`;
    });

    $(document).on('click', '.btn_edit_artikel', function () {
        const id = $(this).data('id');
        const kategoriId = $(this).data('kategori-id');
        const kategoriSlug = $(this).data('kategori-slug');
        window.location.href = `${BASE_KB}/knowledge_base/${holdingCode}/artikel/edit/${kategoriId}/${id}`;
    });

    $(document).on('click', '.btn_hapus_artikel', function() {
        const id    = $(this).data('id');
        const judul = $(this).data('judul');
        showHapusModal('Hapus artikel "' + judul + '"? Tindakan ini tidak dapat dibatalkan.', function() {
            swalLoading();
            $.ajax({
                url: "{{ route('knowledge_base.artikel.destroy', ':id') }}".replace(':id', id),
                type: 'POST',
                data: { _token: TOKEN, _method: 'DELETE' },
                success: function(res) {
                    Swal.close();
                    $('#modal_hapus_konfirmasi').modal('hide');
                    loadArtikelPanel(activeKategoriId, activeKategoriNama);
                    Swal.fire({ icon:'success', title:'Terhapus!', timer:1800, showConfirmButton:false });
                },
                error: function(e) {
                    Swal.close();
                    Swal.fire('Gagal', e.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                }
            });
        });
    });

    function showHapusModal(text, callback) {
        $('#hapus_konfirmasi_text').text(text);
        hapusCallback = callback;
        $('#modal_hapus_konfirmasi').modal('show');
    }

    $('#btn_konfirm_hapus').on('click', function() {
        if (typeof hapusCallback === 'function') hapusCallback();
    });

    $(document).ready(function() {
        loadKategoriTree();
    });
</script>
@endsection