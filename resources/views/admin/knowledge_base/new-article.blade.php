@extends('admin.layouts.dashboard')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<style type="text/css">
    /* ── Card wrapper ── */
    .kb-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
        overflow: hidden;
    }

    .kb-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f0eaff;
        display: flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, #7c3aed08 0%, #ffffff 100%);
    }

    .kb-card-header .icon-wrap {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #ede9fe;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .kb-card-header .icon-wrap i { font-size: 18px; color: #7c3aed; }

    .kb-card-header h5 { margin: 0; font-size: 15px; font-weight: 600; color: #1e1b4b; }
    .kb-card-header p  { margin: 0; font-size: 12px; color: #6b7280; }

    .kb-card-body { padding: 24px; }

    /* ── Form label ── */
    .form-label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px; }
    .form-label .req { color: #7c3aed; margin-left: 2px; }

    /* ── Input focus purple ── */
    .form-control:focus,
    .form-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, .12);
    }
    .form-control[readonly] { background: #f9f8ff; color: #6b7280; }

    /* ── Slug hint ── */
    .slug-hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }

    /* ── Rich Text Toolbar ── */
    .rte-wrap {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        transition: border-color .15s, box-shadow .15s;
    }
    .rte-wrap:focus-within {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, .12);
    }

    .rte-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 2px;
        padding: 7px 10px;
        background: #f9f8ff;
        border-bottom: 1px solid #ede9fe;
    }

    .tb-sep { width: 1px; height: 20px; background: #e5e7eb; margin: 0 4px; }

    .tb-btn {
        width: 30px; height: 30px;
        border: none; border-radius: 6px;
        background: transparent; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: #4b5563; font-size: 13px;
        transition: background .12s, color .12s;
    }
    .tb-btn:hover, .tb-btn.active { background: #ede9fe; color: #7c3aed; }

    .tb-select {
        font-size: 11px; padding: 4px 7px;
        border: 1px solid #e5e7eb; border-radius: 6px;
        background: #fff; color: #374151;
        cursor: pointer; outline: none;
    }
    .tb-select:focus { border-color: #7c3aed; }

    .rte-body {
        min-height: 220px; max-height: 500px;
        overflow-y: auto; padding: 14px 16px;
        font-size: 13.5px; line-height: 1.75;
        color: #111827; outline: none;
    }
    .rte-body:empty::before {
        content: 'Tulis konten artikel di sini...';
        color: #9ca3af; pointer-events: none;
    }
    .rte-body table { border-collapse: collapse; width: 100%; margin: 8px 0; }
    .rte-body table td, .rte-body table th {
        border: 1px solid #d1d5db; padding: 6px 10px; font-size: 13px;
    }

    /* ── RTE Images ── */
    .rte-body img {
        max-width: 100%; height: auto;
        border-radius: 5px; cursor: pointer;
        transition: outline .1s;
    }
    .rte-body img.img-selected {
        outline: 2.5px solid #7c3aed;
        outline-offset: 2px;
    }
    /* alignment via data-align attr */
    .rte-body img[data-align="left"]   { float: left;  margin: 4px 14px 6px 0; }
    .rte-body img[data-align="right"]  { float: right; margin: 4px 0 6px 14px; }
    .rte-body img[data-align="center"] { display: block; margin: 8px auto; float: none; }
    .rte-body img[data-align="inline"] { float: none; display: inline; vertical-align: middle; margin: 0 3px; }

    .rte-footer {
        padding: 5px 12px; background: #f9f8ff;
        border-top: 1px solid #ede9fe;
        display: flex; justify-content: space-between; align-items: center;
    }
    .rte-footer span { font-size: 11px; color: #9ca3af; }

    /* ── Media Uploader ── */
    .upload-zone {
        border: 2px dashed #d8b4fe; border-radius: 10px;
        padding: 28px 20px; text-align: center;
        cursor: pointer; position: relative;
        transition: background .15s, border-color .15s;
        background: #fdfcff;
    }
    .upload-zone:hover, .upload-zone.drag-over { border-color: #7c3aed; background: #f5f3ff; }
    .upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
    .upload-zone .uz-icon { font-size: 30px; color: #7c3aed; margin-bottom: 8px; }
    .upload-zone p { font-size: 13px; color: #4b5563; margin: 0 0 4px; }
    .upload-zone small { font-size: 11px; color: #9ca3af; }

    /* ── File list ── */
    .file-list { display: flex; flex-direction: column; gap: 7px; margin-top: 10px; }

    .file-item {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 12px; background: #f9f8ff;
        border: 1px solid #ede9fe; border-radius: 8px;
    }
    .file-item .fi-icon {
        width: 34px; height: 34px; border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0;
    }
    .fi-img { background: #ede9fe; color: #7c3aed; }
    .fi-pdf { background: #fee2e2; color: #dc2626; }
    .fi-doc { background: #dbeafe; color: #2563eb; }
    .fi-xl  { background: #dcfce7; color: #16a34a; }
    .fi-zip { background: #fef3c7; color: #d97706; }
    .fi-def { background: #f3f4f6; color: #6b7280; }

    .file-item .fi-info { flex: 1; min-width: 0; }
    .fi-info .fi-name { font-size: 12px; font-weight: 600; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .fi-info .fi-meta { font-size: 11px; color: #6b7280; }
    .fi-progress { height: 3px; background: #e9d5ff; border-radius: 2px; margin-top: 5px; overflow: hidden; }
    .fi-progress-bar { height: 100%; background: #7c3aed; border-radius: 2px; transition: width .4s ease; }

    .fi-remove {
        width: 24px; height: 24px; border: none; background: transparent;
        border-radius: 5px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: #9ca3af; font-size: 13px; flex-shrink: 0;
        transition: background .12s, color .12s;
    }
    .fi-remove:hover { background: #fee2e2; color: #dc2626; }

    /* ── Tags ── */
    .tags-box {
        display: flex; flex-wrap: wrap; align-items: center;
        gap: 5px; min-height: 38px; padding: 5px 10px;
        border: 1px solid #dee2e6; border-radius: 6px;
        background: #fff; cursor: text;
        transition: border-color .15s, box-shadow .15s;
    }
    .tags-box:focus-within { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
    .tag-chip { display: flex; align-items: center; gap: 4px; background: #ede9fe; color: #5b21b6; border-radius: 20px; padding: 2px 9px; font-size: 11px; font-weight: 600; }
    .tag-chip-remove { border: none; background: none; padding: 0; cursor: pointer; color: #7c3aed; line-height: 1; font-size: 11px; display: flex; align-items: center; }
    .tag-chip-remove:hover { color: #dc2626; }
    .tag-real-input { border: none; outline: none; background: transparent; font-size: 13px; color: #1f2937; min-width: 80px; flex: 1; }

    /* ── Status toggle ── */
    .status-toggle { display: flex; gap: 8px; }
    .st-btn {
        flex: 1; padding: 8px 12px;
        border: 1px solid #dee2e6; border-radius: 7px;
        background: #f9fafb; font-size: 12px; font-weight: 600;
        color: #6b7280; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        transition: all .15s;
    }
    .st-btn:hover          { border-color: #d8b4fe; }
    .st-btn.active-draft   { border-color: #f59e0b; background: #fffbeb; color: #92400e; }
    .st-btn.active-publik  { border-color: #10b981; background: #ecfdf5; color: #065f46; }

    /* ── Breadcrumb ── */
    .kb-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #9ca3af; margin-bottom: 14px; }
    .kb-breadcrumb a { color: #6b7280; text-decoration: none; }
    .kb-breadcrumb a:hover { color: #7c3aed; }
    .kb-breadcrumb .bc-active { color: #7c3aed; font-weight: 600; }
    .kb-breadcrumb i { font-size: 11px; }

    /* ── Form footer ── */
    .kb-form-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 16px; margin-top: 16px; border-top: 1px solid #f0eaff; }

    /* ── Validation error ── */
    .field-error { font-size: 11px; color: #dc2626; margin-top: 4px; display: none; }
    .field-error.show { display: block; }
    .is-invalid-custom { border-color: #dc2626 !important; box-shadow: none !important; }

    /* ══════════════════════════════════════
       IMAGE MODAL
    ══════════════════════════════════════ */
    .img-modal-backdrop {
        display: none;
        position: fixed; inset: 0;
        background: rgba(17, 24, 39, .5);
        z-index: 1060;
        align-items: center;
        justify-content: center;
    }
    .img-modal-backdrop.open { display: flex; }

    .img-modal {
        background: #fff;
        border-radius: 14px;
        width: 500px;
        max-width: calc(100vw - 32px);
        max-height: calc(100vh - 60px);
        overflow-y: auto;
        box-shadow: 0 24px 64px rgba(0,0,0,.22);
        animation: imgIn .16s ease;
    }
    @keyframes imgIn {
        from { transform: translateY(-14px) scale(.97); opacity: 0; }
        to   { transform: none; opacity: 1; }
    }

    .img-modal-hd {
        padding: 14px 18px;
        border-bottom: 1px solid #f0eaff;
        display: flex; align-items: center; justify-content: space-between;
        background: #faf8ff;
        position: sticky; top: 0; z-index: 1;
    }
    .img-modal-hd h6 {
        margin: 0; font-size: 14px; font-weight: 600; color: #1e1b4b;
        display: flex; align-items: center; gap: 8px;
    }
    .img-modal-hd h6 i { color: #7c3aed; font-size: 15px; }

    .img-modal-x {
        width: 28px; height: 28px; border: none; border-radius: 6px;
        background: transparent; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: #6b7280; font-size: 14px;
        transition: background .12s, color .12s;
    }
    .img-modal-x:hover { background: #fee2e2; color: #dc2626; }

    .img-modal-bd { padding: 18px; }

    /* Source tabs */
    .img-tabs {
        display: flex; gap: 3px;
        background: #f3f4f6; border-radius: 8px; padding: 3px;
        margin-bottom: 14px;
    }
    .img-tab {
        flex: 1; padding: 6px 10px; border: none; border-radius: 6px;
        background: transparent; font-size: 12px; font-weight: 600;
        color: #6b7280; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 5px;
        transition: background .12s, color .12s;
    }
    .img-tab.active { background: #fff; color: #7c3aed; box-shadow: 0 1px 4px rgba(0,0,0,.09); }
    .img-panel { display: none; }
    .img-panel.active { display: block; }

    /* File drop */
    .img-drop {
        border: 2px dashed #d8b4fe; border-radius: 8px;
        padding: 22px 16px; text-align: center; cursor: pointer;
        position: relative; background: #fdfcff;
        transition: background .15s, border-color .15s; margin-bottom: 10px;
    }
    .img-drop:hover, .img-drop.drag-over { border-color: #7c3aed; background: #f5f3ff; }
    .img-drop input { position: absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .img-drop .id-icon { font-size: 26px; color: #7c3aed; margin-bottom: 5px; }
    .img-drop p { font-size: 12px; color: #4b5563; margin: 0; }
    .img-drop small { font-size: 11px; color: #9ca3af; }

    /* Preview */
    .img-preview {
        display: none; margin-bottom: 14px;
        background: #f9f8ff; border-radius: 8px; padding: 10px;
        text-align: center; position: relative;
    }
    .img-preview.show { display: block; }
    .img-preview img { max-height: 160px; max-width: 100%; border-radius: 6px; }
    .img-preview-rm {
        position: absolute; top: 6px; right: 6px;
        width: 22px; height: 22px; border-radius: 50%;
        border: none; background: rgba(220,38,38,.85); color: #fff;
        cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 11px;
    }
    .img-preview-info { font-size: 11px; color: #6b7280; margin-top: 6px; }

    /* Size row */
    .img-size-grid {
        display: grid; grid-template-columns: 1fr 32px 1fr;
        align-items: end; gap: 6px; margin-bottom: 14px;
    }
    .img-size-lbl { font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 4px; }
    .lock-btn {
        width: 32px; height: 32px; /* matches input-sm height */
        border: 1px solid #dee2e6; border-radius: 6px;
        background: #f9fafb; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: #7c3aed; font-size: 13px;
        transition: background .12s, border-color .12s;
        margin-bottom: 0;
    }
    .lock-btn.locked { background: #ede9fe; border-color: #7c3aed; }

    /* Align buttons */
    .img-align-lbl { font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .img-align-row { display: flex; gap: 6px; margin-bottom: 0; }
    .img-align-btn {
        flex: 1; padding: 7px 4px;
        border: 1px solid #dee2e6; border-radius: 7px;
        background: #f9fafb; font-size: 11px; font-weight: 600; color: #6b7280;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px;
        transition: all .12s;
    }
    .img-align-btn i { font-size: 12px; }
    .img-align-btn:hover  { border-color: #d8b4fe; }
    .img-align-btn.active { border-color: #7c3aed; background: #ede9fe; color: #5b21b6; }

    .img-modal-ft {
        padding: 12px 18px;
        border-top: 1px solid #f0eaff;
        display: flex; justify-content: flex-end; gap: 8px;
        background: #faf8ff;
        position: sticky; bottom: 0;
    }
</style>
@endsection


@section('isi')
@include('sweetalert::alert')

<div class="container-xxl container-p-y">

    <div class="kb-breadcrumb">
        <i class="fa-solid fa-house"></i>
        <a href="{{ route('knowledge_base.index', $holding->holding_code) }}">Knowledge Base</a>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('knowledge_base.index', $holding->holding_code) }}?kategoriId={{ $kategori->kb_id }}">{{ $kategori->kb_name }}</a>
        <i class="fa-solid fa-chevron-right"></i>
        <span class="bc-active">Buat Artikel</span>
    </div>

    <div class="kb-card">
        <div class="kb-card-header">
            <div class="icon-wrap">
                <i class="fa-solid fa-file-circle-plus"></i>
            </div>
            <div>
                <h5>Buat Artikel Baru</h5>
                <p>Kategori: <strong>{{ $kategori->kb_name }}</strong></p>
            </div>
        </div>

        <div class="kb-card-body">

            <form id="formArtikel" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="kb_kategori_id" value="{{ $kategori_id }}">

                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label">Judul Artikel <span class="req">*</span></label>
                        <input type="text" class="form-control" id="judul" name="judul"
                            placeholder="Masukkan judul artikel..." required>
                        <div class="field-error" id="err-judul"></div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Slug URL</label>
                        <input type="text" class="form-control" id="slug" name="kba_slug"
                            readonly placeholder="otomatis dari judul">
                        <div class="slug-hint">
                            <i class="fa-solid fa-circle-info"></i>
                            Dibuat otomatis dari judul artikel
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konten Artikel <span class="req">*</span></label>
                    <div class="rte-wrap" id="rteWrap">
                        <div class="rte-toolbar">
                            <select class="tb-select" id="blockFmt" title="Format paragraf">
                                <option value="p">Paragraf</option>
                                <option value="h1">Heading 1</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3">Heading 3</option>
                                <option value="blockquote">Kutipan</option>
                                <option value="pre">Kode</option>
                            </select>

                            <div class="tb-sep"></div>

                            <button type="button" class="tb-btn" data-cmd="bold"          title="Tebal (Ctrl+B)"><i class="fa-solid fa-bold"></i></button>
                            <button type="button" class="tb-btn" data-cmd="italic"        title="Miring (Ctrl+I)"><i class="fa-solid fa-italic"></i></button>
                            <button type="button" class="tb-btn" data-cmd="underline"     title="Garis bawah (Ctrl+U)"><i class="fa-solid fa-underline"></i></button>
                            <button type="button" class="tb-btn" data-cmd="strikeThrough" title="Coret"><i class="fa-solid fa-strikethrough"></i></button>

                            <div class="tb-sep"></div>

                            <button type="button" class="tb-btn" data-cmd="justifyLeft"   title="Rata kiri"><i class="fa-solid fa-align-left"></i></button>
                            <button type="button" class="tb-btn" data-cmd="justifyCenter" title="Rata tengah"><i class="fa-solid fa-align-center"></i></button>
                            <button type="button" class="tb-btn" data-cmd="justifyRight"  title="Rata kanan"><i class="fa-solid fa-align-right"></i></button>
                            <button type="button" class="tb-btn" data-cmd="justifyFull"   title="Rata penuh"><i class="fa-solid fa-align-justify"></i></button>

                            <div class="tb-sep"></div>

                            <button type="button" class="tb-btn" data-cmd="insertUnorderedList" title="Bullet list"><i class="fa-solid fa-list-ul"></i></button>
                            <button type="button" class="tb-btn" data-cmd="insertOrderedList"   title="Nomor list"><i class="fa-solid fa-list-ol"></i></button>
                            <button type="button" class="tb-btn" data-cmd="indent"              title="Tambah indent"><i class="fa-solid fa-indent"></i></button>
                            <button type="button" class="tb-btn" data-cmd="outdent"             title="Kurangi indent"><i class="fa-solid fa-outdent"></i></button>

                            <div class="tb-sep"></div>

                            <button type="button" class="tb-btn" id="btnLink"  title="Sisipkan link"><i class="fa-solid fa-link"></i></button>
                            <button type="button" class="tb-btn" id="btnImage" title="Sisipkan gambar"><i class="fa-solid fa-image"></i></button>
                            <button type="button" class="tb-btn" id="btnTable" title="Sisipkan tabel"><i class="fa-solid fa-table"></i></button>
                            <button type="button" class="tb-btn" id="btnHr"    title="Garis pemisah"><i class="fa-solid fa-minus"></i></button>

                            <div class="tb-sep"></div>

                            <button type="button" class="tb-btn" data-cmd="undo" title="Undo (Ctrl+Z)"><i class="fa-solid fa-rotate-left"></i></button>
                            <button type="button" class="tb-btn" data-cmd="redo" title="Redo (Ctrl+Y)"><i class="fa-solid fa-rotate-right"></i></button>

                            <div class="tb-sep"></div>

                            <button type="button" class="tb-btn" data-cmd="removeFormat" title="Hapus format"><i class="fa-solid fa-eraser"></i></button>

                        </div>

                        <div class="rte-body" id="rteEditor"
                            contenteditable="true"
                            aria-label="Konten artikel"
                            aria-multiline="true">
                        </div>

                        <div class="rte-footer">
                            <span><i class="fa-solid fa-keyboard"></i> Tekan Tab untuk lanjut ke field berikutnya</span>
                            <span id="wordCount">0 kata</span>
                        </div>

                    </div>

                    <textarea id="konten" name="konten" hidden></textarea>
                    <div class="field-error" id="err-konten"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa-solid fa-paperclip"></i> Lampiran & Media
                        <small class="text-muted fw-normal ms-1">(opsional)</small>
                    </label>
                    <div class="upload-zone" id="dropZone">
                        <input type="file" id="attachment" name="attachment[]" multiple
                            accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.zip">
                        <div class="uz-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                        <p>Klik atau <strong>drag &amp; drop</strong> file di sini</p>
                        <small>Mendukung: JPG, PNG, PDF, DOC, XLS, ZIP &middot; Maks. 10 MB per file</small>
                    </div>
                    <div class="file-list" id="fileList"></div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label">
                            <i class="fa-solid fa-tags"></i> Tags
                            <small class="text-muted fw-normal ms-1">(opsional, pisah dengan Enter)</small>
                        </label>
                        <div class="tags-box" id="tagsBox">
                            <input class="tag-real-input" id="tagInput"
                                placeholder="Ketik tag lalu tekan Enter..."
                                autocomplete="off">
                        </div>
                        <input type="hidden" name="tags" id="tagsHidden">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">
                            <i class="fa-solid fa-toggle-on"></i> Status Publikasi <span class="req">*</span>
                        </label>
                        <div class="status-toggle">
                            <button type="button" class="st-btn active-draft" data-val="draft" id="btnDraft">
                                <i class="fa-solid fa-pencil"></i> Draft
                            </button>
                            <button type="button" class="st-btn" data-val="publik" id="btnPublik">
                                <i class="fa-solid fa-globe"></i> Publik
                            </button>
                        </div>
                        <input type="hidden" name="status" id="statusHidden" value="draft">
                        <div class="field-error" id="err-status"></div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="form-label">
                            <i class="fa-solid fa-lock"></i> Akses Artikel
                        </label>
                        <select class="form-select form-select-sm" id="accessOverride" name="access_override">
                            <option value="inherit" selected>Ikut Kategori (default)</option>
                            <option value="public">Publik untuk Semua</option>
                            <option value="restricted">Dibatasi Departemen Tertentu</option>
                        </select>
                    </div>
                    <div class="col-md-7" id="artDepartmentsWrap" style="display:none;">
                        <label class="form-label">Departemen yang Diizinkan</label>
                        <div id="artDepartmentsList" class="d-flex flex-wrap gap-2" style="max-height:120px; overflow-y:auto; border:1px solid #e5e7eb; border-radius:8px; padding:10px;">
                            <span class="text-muted" style="font-size:12px;">Memuat departemen...</span>
                        </div>
                    </div>
                </div>

                <div class="kb-form-footer">
                    <a href="{{ route('knowledge_base.index', $holding) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-primary btn-sm" id="btnSave">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="img-modal-backdrop" id="imgBackdrop" role="dialog" aria-modal="true" aria-labelledby="imgModalTitle">
    <div class="img-modal">
        <div class="img-modal-hd">
            <h6 id="imgModalTitle">
                <i class="fa-solid fa-image"></i>
                <span id="imgModalLabel">Sisipkan Gambar</span>
            </h6>
            <button type="button" class="img-modal-x" id="imgClose" aria-label="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="img-modal-bd">
            <div class="img-tabs">
                <button type="button" class="img-tab active" data-panel="file">
                    <i class="fa-solid fa-upload"></i> Upload File
                </button>
                <button type="button" class="img-tab" data-panel="url">
                    <i class="fa-solid fa-link"></i> URL Gambar
                </button>
            </div>

            <div class="img-panel active" id="imgPanelFile">
                <div class="img-drop" id="imgDrop">
                    <input type="file" id="imgFile" accept="image/jpeg,image/png,image/gif,image/webp">
                    <div class="id-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <p>Klik atau drag gambar ke sini</p>
                    <small>JPG, PNG, GIF, WebP &middot; Maks. 5 MB</small>
                </div>
            </div>

            <div class="img-panel" id="imgPanelUrl">
                <label class="form-label">URL Gambar</label>
                <div class="input-group input-group-sm mb-2">
                    <input type="text" class="form-control" id="imgUrl"
                        placeholder="https://example.com/photo.jpg">
                    <button class="btn btn-outline-secondary" type="button" id="btnPreviewUrl">
                        <i class="fa-solid fa-eye"></i> Preview
                    </button>
                </div>
            </div>

            <div class="img-preview" id="imgPreview">
                <img src="" id="imgPreviewImg" alt="Preview gambar">
                <button type="button" class="img-preview-rm" id="imgClearPreview" title="Hapus">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div class="img-preview-info" id="imgPreviewInfo"></div>
            </div>

            <hr class="my-3" style="border-color:#f0eaff">

            <div class="img-size-grid">
                <div>
                    <div class="img-size-lbl">Lebar</div>
                    <input type="text" class="form-control form-control-sm" id="imgW"
                        placeholder="mis. 400px atau 80%">
                </div>
                <div class="d-flex align-items-end pb-1">
                    <button type="button" class="lock-btn locked" id="imgLock" title="Kunci rasio">
                        <i class="fa-solid fa-lock" id="imgLockIcon"></i>
                    </button>
                </div>
                <div>
                    <div class="img-size-lbl">Tinggi</div>
                    <input type="text" class="form-control form-control-sm" id="imgH"
                        placeholder="mis. 300px">
                </div>
            </div>

            <div class="img-align-lbl">Posisi / Alignment</div>
            <div class="img-align-row">
                <button type="button" class="img-align-btn" data-align="left">
                    <i class="fa-solid fa-arrow-left"></i> Kiri
                </button>
                <button type="button" class="img-align-btn active" data-align="center">
                    <i class="fa-solid fa-align-center"></i> Tengah
                </button>
                <button type="button" class="img-align-btn" data-align="right">
                    <i class="fa-solid fa-arrow-right"></i> Kanan
                </button>
                <button type="button" class="img-align-btn" data-align="inline">
                    <i class="fa-solid fa-text-width"></i> Inline
                </button>
            </div>

        </div>

        <div class="img-modal-ft">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="imgCancel">Batal</button>
            <button type="button" class="btn btn-primary btn-sm" id="imgApply" disabled>
                <i class="fa-solid fa-check"></i>
                <span id="imgApplyLabel">Sisipkan</span>
            </button>
        </div>

    </div>
</div>

@endsection


@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>
    function swalLoading(text = 'Memproses...') {
        Swal.fire({
            title: text,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });
    }

    $('#judul').on('input', function () {
        const slug = $(this).val()
            .toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/(^-|-$)/g, '');
        $('#slug').val(slug);
    });

    const rte = document.getElementById('rteEditor');

    document.querySelectorAll('.tb-btn[data-cmd]').forEach(btn => {
        btn.addEventListener('mousedown', e => {
            e.preventDefault();
            document.execCommand(btn.dataset.cmd, false, null);
            syncToolbar();
        });
    });

    document.getElementById('blockFmt').addEventListener('change', function () {
        document.execCommand('formatBlock', false, '<' + this.value + '>');
        rte.focus();
    });

    document.getElementById('btnLink').addEventListener('mousedown', e => {
        e.preventDefault();
        const url = prompt('Masukkan URL:', 'https://');
        if (url) document.execCommand('createLink', false, url);
    });

    document.getElementById('btnTable').addEventListener('mousedown', e => {
        e.preventDefault();
        const html = `<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                      <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table><p></p>`;
        document.execCommand('insertHTML', false, html);
    });

    document.getElementById('btnHr').addEventListener('mousedown', e => {
        e.preventDefault();
        document.execCommand('insertHorizontalRule', false, null);
    });

    function syncToolbar() {
        document.querySelectorAll('.tb-btn[data-cmd]').forEach(btn => {
            btn.classList.toggle('active', document.queryCommandState(btn.dataset.cmd));
        });
    }
    rte.addEventListener('keyup',   syncToolbar);
    rte.addEventListener('mouseup', syncToolbar);

    rte.addEventListener('input', () => {
        const text  = rte.innerText.trim();
        const words = text ? text.split(/\s+/).filter(w => w).length : 0;
        document.getElementById('wordCount').textContent = words + ' kata';
        clearFieldError('konten');
        document.getElementById('rteWrap').classList.remove('is-invalid-custom');
    });

    rte.addEventListener('keydown', e => {
        if (e.key === 'Tab') {
            e.preventDefault();
            document.getElementById('attachment').focus();
        }
    });

    (function () {
        const backdrop    = document.getElementById('imgBackdrop');
        const modalLabel  = document.getElementById('imgModalLabel');
        const applyBtn    = document.getElementById('imgApply');
        const applyLabel  = document.getElementById('imgApplyLabel');

        const tabs        = document.querySelectorAll('.img-tab');
        const panelFile   = document.getElementById('imgPanelFile');
        const panelUrl    = document.getElementById('imgPanelUrl');

        const imgDrop     = document.getElementById('imgDrop');
        const imgFile     = document.getElementById('imgFile');
        const imgUrlIn    = document.getElementById('imgUrl');
        const btnPrevUrl  = document.getElementById('btnPreviewUrl');

        const preview     = document.getElementById('imgPreview');
        const previewImg  = document.getElementById('imgPreviewImg');
        const previewInfo = document.getElementById('imgPreviewInfo');
        const clearPrev   = document.getElementById('imgClearPreview');

        const wInput      = document.getElementById('imgW');
        const hInput      = document.getElementById('imgH');
        const lockBtn     = document.getElementById('imgLock');
        const lockIcon    = document.getElementById('imgLockIcon');

        const alignBtns   = document.querySelectorAll('.img-align-btn');

        /* State */
        let locked      = true;
        let natW = 0, natH = 0;
        let currentSrc  = '';
        let editTarget  = null;
        let savedRange  = null;

        function openModal(target = null) {
            const sel = window.getSelection();
            if (sel && sel.rangeCount) savedRange = sel.getRangeAt(0).cloneRange();

            editTarget = target;
            resetModal();

            if (target) {
                modalLabel.textContent  = 'Edit Gambar';
                applyLabel.textContent  = 'Update';
                loadPreview(target.src, target.src.startsWith('data:') ? null : target.src);
                wInput.value = target.style.width  || (target.getAttribute('width')  ? target.getAttribute('width')+'px'  : '');
                hInput.value = target.style.height || (target.getAttribute('height') ? target.getAttribute('height')+'px' : '');
                const al = target.getAttribute('data-align') || 'center';
                alignBtns.forEach(b => b.classList.toggle('active', b.dataset.align === al));
            } else {
                modalLabel.textContent = 'Sisipkan Gambar';
                applyLabel.textContent = 'Sisipkan';
            }

            backdrop.classList.add('open');
        }

        function closeModal() { backdrop.classList.remove('open'); }

        function resetModal() {
            currentSrc = ''; natW = 0; natH = 0;
            preview.classList.remove('show');
            previewImg.src = ''; previewInfo.textContent = '';
            wInput.value = ''; hInput.value = '';
            imgUrlIn.value = ''; imgFile.value = '';
            applyBtn.disabled = true;
            switchTab('file');
            alignBtns.forEach(b => b.classList.toggle('active', b.dataset.align === 'center'));
        }

        function switchTab(panel) {
            tabs.forEach(t => t.classList.toggle('active', t.dataset.panel === panel));
            panelFile.classList.toggle('active', panel === 'file');
            panelUrl.classList.toggle('active',  panel === 'url');
        }

        function loadPreview(src, label) {
            currentSrc = src;
            previewImg.src = src;
            previewImg.onload = () => {
                natW = previewImg.naturalWidth;
                natH = previewImg.naturalHeight;
                preview.classList.add('show');
                applyBtn.disabled = false;
                previewInfo.textContent = natW && natH ? `${natW} × ${natH} px` : '';
                if (!wInput.value) wInput.value = Math.min(natW, 600) + 'px';
                if (!hInput.value && locked) hInput.value = calcH(wInput.value);
            };
            previewImg.onerror = () => {
                preview.classList.remove('show');
                applyBtn.disabled = true;
                alert('Gagal memuat gambar. Periksa URL atau pilih file lain.');
            };
        }

        function calcH(wVal) {
            if (!natW || !natH) return '';
            const n = parseFloat(wVal);
            return (!isNaN(n) && n > 0) ? Math.round(n * natH / natW) + 'px' : '';
        }
        function calcW(hVal) {
            if (!natW || !natH) return '';
            const n = parseFloat(hVal);
            return (!isNaN(n) && n > 0) ? Math.round(n * natW / natH) + 'px' : '';
        }

        function activeAlign() {
            const a = document.querySelector('.img-align-btn.active');
            return a ? a.dataset.align : 'center';
        }

        function applyStyleToImg(imgEl) {
            const w  = wInput.value.trim();
            const h  = hInput.value.trim();
            const al = activeAlign();

            imgEl.removeAttribute('width');
            imgEl.removeAttribute('height');
            imgEl.style.cssText = '';

            if (w) imgEl.style.width  = w;
            if (h) imgEl.style.height = h;

            switch (al) {
                case 'left':
                    imgEl.style.float   = 'left';
                    imgEl.style.margin  = '4px 14px 6px 0';
                    break;
                case 'right':
                    imgEl.style.float   = 'right';
                    imgEl.style.margin  = '4px 0 6px 14px';
                    break;
                case 'center':
                    imgEl.style.display = 'block';
                    imgEl.style.margin  = '8px auto';
                    imgEl.style.float   = 'none';
                    break;
                case 'inline':
                    imgEl.style.float           = 'none';
                    imgEl.style.display         = 'inline';
                    imgEl.style.verticalAlign   = 'middle';
                    imgEl.style.margin          = '0 3px';
                    break;
            }
            imgEl.setAttribute('data-align', al);
        }

        function doApply() {
            if (!currentSrc) return;

            if (editTarget) {
                editTarget.src = currentSrc;
                applyStyleToImg(editTarget);
                editTarget.classList.remove('img-selected');
            } else {
                rte.focus();
                const sel = window.getSelection();
                if (savedRange) { sel.removeAllRanges(); sel.addRange(savedRange); }

                const imgEl = document.createElement('img');
                imgEl.src = currentSrc;
                imgEl.alt = '';
                applyStyleToImg(imgEl);

                const range = sel.getRangeAt(0);
                range.deleteContents();

                const al = activeAlign();
                if (al === 'center') {
                    const p = document.createElement('p');
                    p.appendChild(imgEl);
                    range.insertNode(p);
                    range.setStartAfter(p);
                } else {
                    range.insertNode(imgEl);
                    range.setStartAfter(imgEl);
                }

                range.collapse(true);
                sel.removeAllRanges();
                sel.addRange(range);
            }

            closeModal();
        }

        rte.addEventListener('click', e => {
            if (e.target.tagName === 'IMG') {
                rte.querySelectorAll('.img-selected').forEach(i => i.classList.remove('img-selected'));
                e.target.classList.add('img-selected');
                openModal(e.target);
            } else {
                rte.querySelectorAll('.img-selected').forEach(i => i.classList.remove('img-selected'));
            }
        });

        document.getElementById('btnImage').addEventListener('mousedown', e => {
            e.preventDefault();
            openModal(null);
        });

        tabs.forEach(t => t.addEventListener('click', () => switchTab(t.dataset.panel)));

        async function handleFile(file) {
            if (!file.type.startsWith('image/')) { alert('File harus berupa gambar.'); return; }
            if (file.size > 5 * 1024 * 1024)    { alert('Ukuran gambar maksimal 5 MB.'); return; }
            const reader = new FileReader();
            reader.onload = e => loadPreview(e.target.result, file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)');
            reader.readAsDataURL(file);
        }

        imgFile.addEventListener('change', function () { if (this.files[0]) handleFile(this.files[0]); });

        imgDrop.addEventListener('dragover',  ev => { ev.preventDefault(); imgDrop.classList.add('drag-over'); });
        imgDrop.addEventListener('dragleave', ()  => imgDrop.classList.remove('drag-over'));
        imgDrop.addEventListener('drop', ev => {
            ev.preventDefault();
            imgDrop.classList.remove('drag-over');
            if (ev.dataTransfer.files[0]) handleFile(ev.dataTransfer.files[0]);
        });

        function tryLoadUrl() {
            const url = imgUrlIn.value.trim();
            if (url) loadPreview(url, url);
        }
        btnPrevUrl.addEventListener('click', tryLoadUrl);
        imgUrlIn.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); tryLoadUrl(); } });

        clearPrev.addEventListener('click', () => {
            currentSrc = ''; natW = 0; natH = 0;
            preview.classList.remove('show');
            previewImg.src = ''; previewInfo.textContent = '';
            applyBtn.disabled = true;
            wInput.value = ''; hInput.value = '';
            imgFile.value = ''; imgUrlIn.value = '';
        });

        wInput.addEventListener('input', () => {
            if (locked) hInput.value = calcH(wInput.value);
        });
        hInput.addEventListener('input', () => {
            if (locked) wInput.value = calcW(hInput.value);
        });

        lockBtn.addEventListener('click', () => {
            locked = !locked;
            lockBtn.classList.toggle('locked', locked);
            lockIcon.className = locked ? 'fa-solid fa-lock' : 'fa-solid fa-lock-open';
        });

        alignBtns.forEach(b => b.addEventListener('click', () => {
            alignBtns.forEach(x => x.classList.remove('active'));
            b.classList.add('active');
        }));

        document.getElementById('imgClose').addEventListener('click',  closeModal);
        document.getElementById('imgCancel').addEventListener('click', closeModal);
        backdrop.addEventListener('click', e => { if (e.target === backdrop) closeModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && backdrop.classList.contains('open')) closeModal(); });
        applyBtn.addEventListener('click', doApply);

    })();

    const dropZone  = document.getElementById('dropZone');
    const fileInput = document.getElementById('attachment');
    const fileList  = document.getElementById('fileList');
    let uploadedFiles = [];

    function getFileIcon(name) {
        const ext = name.split('.').pop().toLowerCase();
        if (['jpg','jpeg','png','gif','webp','svg'].includes(ext)) return { cls: 'fi-img', icon: 'fa-solid fa-image' };
        if (ext === 'pdf')                                          return { cls: 'fi-pdf', icon: 'fa-solid fa-file-pdf' };
        if (['doc','docx'].includes(ext))                          return { cls: 'fi-doc', icon: 'fa-solid fa-file-word' };
        if (['xls','xlsx'].includes(ext))                          return { cls: 'fi-xl',  icon: 'fa-solid fa-file-excel' };
        if (ext === 'zip')                                          return { cls: 'fi-zip', icon: 'fa-solid fa-file-zipper' };
        return { cls: 'fi-def', icon: 'fa-solid fa-file' };
    }

    function formatSize(bytes) {
        if (bytes < 1024)    return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function renderFiles() {
        fileList.innerHTML = '';
        uploadedFiles.forEach((f, i) => {
            const fi   = getFileIcon(f.name);
            const item = document.createElement('div');
            item.className = 'file-item';
            item.innerHTML = `
                <div class="fi-icon ${fi.cls}"><i class="${fi.icon}"></i></div>
                <div class="fi-info">
                    <div class="fi-name">${f.name}</div>
                    <div class="fi-meta">${formatSize(f.size)}</div>
                    <div class="fi-progress">
                        <div class="fi-progress-bar" id="fpb${i}" style="width:0%"></div>
                    </div>
                </div>
                <button type="button" class="fi-remove" data-i="${i}" title="Hapus file">
                    <i class="fa-solid fa-xmark"></i>
                </button>`;
            fileList.appendChild(item);
            let p = 0;
            const iv = setInterval(() => {
                p += Math.random() * 25 + 10;
                if (p >= 100) { p = 100; clearInterval(iv); }
                const el = document.getElementById('fpb' + i);
                if (el) el.style.width = Math.min(p, 100) + '%';
            }, 120);
        });
        fileList.querySelectorAll('.fi-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                uploadedFiles.splice(+btn.dataset.i, 1);
                renderFiles();
            });
        });
    }

    fileInput.addEventListener('change', function () {
        uploadedFiles = [...uploadedFiles, ...Array.from(this.files)];
        renderFiles();
    });
    dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', ()  => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        uploadedFiles = [...uploadedFiles, ...Array.from(e.dataTransfer.files)];
        renderFiles();
    });
    const tagInput   = document.getElementById('tagInput');
    const tagsHidden = document.getElementById('tagsHidden');
    let tags = [];
    function renderTags() {
        document.querySelectorAll('.tag-chip').forEach(c => c.remove());
        const box = document.getElementById('tagsBox');
        tags.forEach((t, i) => {
            const chip = document.createElement('span');
            chip.className = 'tag-chip';
            chip.innerHTML = `${t}
                <button type="button" class="tag-chip-remove" data-i="${i}" aria-label="Hapus tag ${t}">
                    <i class="fa-solid fa-xmark"></i>
                </button>`;
            chip.querySelector('button').addEventListener('click', () => { tags.splice(i, 1); renderTags(); });
            box.insertBefore(chip, tagInput);
        });
        tagsHidden.value = tags.join(',');
    }
    tagInput.addEventListener('keydown', e => {
        if ((e.key === 'Enter' || e.key === ',') && tagInput.value.trim()) {
            e.preventDefault();
            const t = tagInput.value.replace(',', '').trim();
            if (t && !tags.includes(t)) { tags.push(t); renderTags(); }
            tagInput.value = '';
        }
        if (e.key === 'Backspace' && !tagInput.value && tags.length) { tags.pop(); renderTags(); }
    });
    document.getElementById('tagsBox').addEventListener('click', () => tagInput.focus());
    document.querySelectorAll('.st-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.st-btn').forEach(b => b.classList.remove('active-draft', 'active-publik'));
            btn.classList.add(btn.dataset.val === 'draft' ? 'active-draft' : 'active-publik');
            document.getElementById('statusHidden').value = btn.dataset.val;
        });
    });
    function showFieldError(field, msg) {
        const el = document.getElementById('err-' + field);
        if (el) { el.textContent = msg; el.classList.add('show'); }
    }
    function clearFieldError(field) {
        const el = document.getElementById('err-' + field);
        if (el) { el.textContent = ''; el.classList.remove('show'); }
    }
    function showValidationErrors(errors) {
        const fieldMap = {
            judul: 'judul', konten: 'konten', status: 'status',
            kb_kategori_id: 'kategori', kba_slug: 'slug',
            kba_konten: 'konten', kba_status: 'status', kba_tags: 'tags',
        };
        Object.entries(errors).forEach(([field, messages]) => {
            const target = fieldMap[field] ?? field;
            showFieldError(target, messages[0]);
            if (field === 'konten') document.getElementById('rteWrap').classList.add('is-invalid-custom');
        });
    }
    let artDeptCache = null;
    function loadArtDepartments(selectedIds = []) {
        const render = (list) => {
            let html = '';
            list.forEach(d => {
                const checked = selectedIds.includes(d.id) ? 'checked' : '';
                html += `
                    <label style="font-size:12px; display:flex; align-items:center; gap:5px; background:#f5f3ff; padding:4px 10px; border-radius:20px; cursor:pointer;">
                        <input type="checkbox" class="art_dept_checkbox" value="${d.id}" ${checked}>
                        ${d.nama_departemen}
                    </label>`;
            });
            $('#artDepartmentsList').html(html || '<span class="text-muted" style="font-size:12px;">Belum ada data departemen.</span>');
        };
        if (artDeptCache) { render(artDeptCache); return; }
        $.get("{{ route('knowledge_base.departemen', $holding->holding_code) }}", function (res) {
            artDeptCache = res || [];
            render(artDeptCache);
        });
    }

    $('#accessOverride').on('change', function () {
        const isRestricted = $(this).val() === 'restricted';
        $('#artDepartmentsWrap').toggle(isRestricted);
        if (isRestricted) loadArtDepartments();
    });

    $('#btnSave').on('click', function (e) {
        e.preventDefault();
        ['judul', 'konten', 'status'].forEach(f => clearFieldError(f));
        document.getElementById('rteWrap').classList.remove('is-invalid-custom');
        document.getElementById('konten').value = rte.innerHTML.trim();

        if (!rte.innerText.trim()) {
            showFieldError('konten', 'Konten artikel tidak boleh kosong.');
            document.getElementById('rteWrap').classList.add('is-invalid-custom');
            rte.focus();
            return;
        }

        const formData = new FormData(document.getElementById('formArtikel'));
        formData.delete('attachment[]');
        uploadedFiles.forEach(f => formData.append('attachment[]', f));
        $('.art_dept_checkbox:checked').each(function () {
            formData.append('departments[]', $(this).val());
        });

        const BASE_KB = "{{ route('knowledge_base.index', $holding->holding_code) }}";

        $.ajax({
            url: "{{ route('knowledge_base.artikel.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend() { swalLoading('Masih dalam Proses Menyimpan Artikel...'); },
            success(res) {
                if (res.code === 200) {
                    Swal.close();
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message ?? 'Artikel berhasil disimpan.',
                            confirmButtonColor: '#7c3aed',
                            timer: 1800,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = BASE_KB;
                        });
                    }, 300);
                }
            },
            error(xhr) {
                Swal.close();
                setTimeout(() => {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        showValidationErrors(xhr.responseJSON.errors);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message ?? 'Terjadi kesalahan.',
                            confirmButtonColor: '#7c3aed',
                        });
                    }
                }, 300);
            }
        });
    });
</script>
@endsection