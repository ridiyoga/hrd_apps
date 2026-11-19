<link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<style>
    .swal2-container {
        z-index: 9999;
    }

    /* ukuran teks di area pilihan (input select2) */
    .select2-container--bootstrap-5 .select2-selection {
        font-size: 0.875rem !important;
        /* Bootstrap small (14px) */
        min-height: calc(1.5em + 0.75rem + 2px);
        /* biar tinggi konsisten */
    }

    /* ukuran teks di dropdown list */
    .select2-container--bootstrap-5 .select2-results__option {
        font-size: 0.875rem !important;
    }

    /* Fokus warna primary */
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: var(--bs-primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25) !important;
    }

    /* Background dan teks saat option terpilih */
    .select2-container--bootstrap-5 .select2-results__option--selected {
        background-color: var(--bs-primary) !important;
        color: #fff !important;
    }

    /* Hover option */
    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
        color: var(--bs-primary) !important;
    }

    /* ukuran huruf untuk pilihan yang sudah dipilih (tag dalam box) */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
        font-size: 0.75rem;
        /* kecilin text */
        padding: 2px 6px;
        /* biar nggak terlalu tinggi */
        line-height: 1.2;
    }

    /* icon "x" di tag */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        font-size: 0.7rem;
        margin-right: 2px;
    }

    /* tulisan placeholder / hasil render */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        font-size: 0.8rem;
    }


    /* ===============================
                                                                                                                           SELECT LOADING
                                                                                                                        ================================ */
    .select-loader {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        z-index: 5;
        pointer-events: none;
    }

    /* ===============================
                                                                                                                           DOT LOADER (3 DOT)
                                                                                                                        ================================ */
    .dot-loader {
        display: flex;
        gap: 4px;
    }

    .dot-loader span {
        width: 6px;
        height: 6px;
        background-color: #7367f0;
        border-radius: 50%;
        opacity: 0.3;
        animation: dotPulse 1.2s infinite ease-in-out;
    }

    .dot-loader span:nth-child(1) {
        animation-delay: 0s;
    }

    .dot-loader span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .dot-loader span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes dotPulse {
        0% {
            opacity: 0.3;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0.3;
        }
    }

    /* Container Styling */
    .shadow_effect {
        padding: 20px;
        background-color: #f8f9fa;
    }


    .card.shadow-sm:hover {
        box-shadow: 0 8px 24px rgba(149, 157, 165, 0.2) !important;
    }

    .card-header {
        font-weight: 600;
        border-bottom: 1px solid rgba(0, 0, 0, .05);
        padding: 1rem 1.25rem;
    }

    /* Form Floating Outline Customization */
    .form-floating-outline {
        position: relative;
    }



    .form-select.border-bottom:focus {
        border-bottom: 2px solid #6f24b6 !important;
        /* Warna biru primary */
        box-shadow: none;
    }

    /* Table Styling */
    .table thead th {
        background-color: #f2f4f7;
        color: #566a7f;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        font-weight: 700;
        border-top: none;
    }

    .table tbody td {
        font-size: 0.675rem;
        font-weight: 450;
        color: #697a8d;
        vertical-align: middle;
        padding: 12px 15px;
    }

    /* Button Styling */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 8px 20px;
        transition: transform 0.1s ease;
    }

    .btn:active {
        transform: scale(0.96);
    }

    .btn-info {
        color: #fff;
        background-color: #03c3ec;
        border-color: #03c3ec;
    }

    /* Alert/Error Styling */
    .text-danger {
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 5px;
    }

    /* Badge-like Look for Table Actions */
    .table .btn-sm {
        padding: 4px 8px;
        font-size: 0.75rem;
    }

    /* Animasi untuk transisi tab atau konten */
    .fade {
        transition: opacity 0.15s linear;
    }

    /* Styling Tab Pills */
    .nav-pills .nav-link {
        color: #697a8d;
        background-color: #f0f2f4;
        border-radius: 6px;
        font-weight: 500;
    }

    .nav-pills .nav-link.active {
        background-color: #5a8dee;
        color: #fff;
        box-shadow: 0 2px 4px rgba(90, 141, 238, 0.4);
    }

    /* Background subtle untuk variasi */
    .bg-light-subtle {
        background-color: #fafbfc;
    }

    .bg-warning-subtle {
        background-color: #fff8e1;
    }

    /* Table Khusus */
    .table-sm td,
    .table-sm th {
        padding: 10px 8px;
        font-size: 0.85rem;
    }

    .badge.bg-label-secondary {
        background-color: #f1f2f4;
        color: #8592a3;
    }

    /* Styling Kartu Dokumen */
    .document-card {
        transition: all 0.2s ease-in-out;
        background-color: #ffffff;
    }

    .document-card:hover {
        border-color: #696cff !important;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.1);
    }

    /* Border putus-putus untuk area upload */
    .border-dashed {
        border: 2px dashed #d9dee3 !important;
        transition: background 0.2s;
    }

    .border-dashed:hover {
        background-color: #f5f5f9;
        border-color: #696cff !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Tombol kecil khusus icon saja */
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-label-secondary {
        background-color: #f0f2f4;
        color: #8592a3;
        border: none;
    }

    .btn-label-secondary:hover {
        background-color: #e1e4e8;
    }


    .document-card .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(105, 108, 255, 0.3) !important;
    }

    .text-xs {
        font-size: 0.7rem;
    }

    .bg-light-subtle {
        background-color: #f9fafb !important;
    }

    .modern-selector-card {
        border: 1px solid #e0e6ed !important;
        border-radius: 16px !important;
        background: #ffffff !important;
        transition: all 0.3s ease;
    }

    /* Mengubah tampilan Button Group agar terlihat seperti toggle modern */
    .modern-btn-group {
        background-color: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        display: flex;
    }

    .modern-btn-group .btn-check+.btn {
        border: none !important;
        border-radius: 8px !important;
        padding: 10px 15px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #64748b;
        background: transparent;
        transition: all 0.2s ease-in-out;
        flex: 1;
    }

    /* Styling saat tombol terpilih (Checked) */
    .modern-btn-group .btn-check:checked+.btn-outline-success {
        background-color: #ffffff !important;
        color: #10b981 !important;
        shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
    }

    .modern-btn-group .btn-check:checked+.btn-outline-primary {
        background-color: #ffffff !important;
        color: #3b82f6 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
    }

    .modern-btn-group .btn-check:checked+.btn-outline-secondary {
        background-color: #ffffff !important;
        color: #475569 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
    }

    /* Label styling */
    .label-modern {
        letter-spacing: 0.5px;
        color: #94a3b8;
    }

    .swal2-popup {
        font-family: 'Inter', 'Public Sans', sans-serif !important;
        border-radius: 15px !important;
    }

    .swal2-title {
        color: #5d596c !important;
    }

    /* Supaya tidak terlihat seperti spinner bawaan Bootstrap atau SweetAlert yang membosankan, */
    /* kita bisa mengganti loader default tersebut dengan CSS Loading Animation yang lebih modern (seperti gaya Double Ring atau Pulse). Berikut adalah cara merombak total visual loading-nya: 1. Custom CSS (Animasi Modern) Tambahkan CSS ini untuk menciptakan spinner kustom yang jauh lebih halus dan bergaya teknologi modern. CSS <style> */

    /* Menghilangkan spinner default Swal */
    .swal2-loader {
        display: none !important;
    }

    /* Container kustom untuk loader */
    .custom-loader-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px 0;
    }

    /* Animasi Double Ring yang Elegan */
    .modern-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid rgba(102, 108, 255, 0.1);
        border-left-color: #666cff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        position: relative;
    }

    .modern-spinner::before {
        content: "";
        position: absolute;
        top: 5px;
        left: 5px;
        right: 5px;
        bottom: 5px;
        border: 4px solid rgba(102, 108, 255, 0.1);
        border-left-color: #00cfe8;
        border-radius: 50%;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Efek teks berdenyut (Pulse) */
    .pulse-text {
        animation: pulse 1.5s ease-in-out infinite;
        font-weight: 500;
        color: #5d596c;
        letter-spacing: 1px;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .badge-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
        vertical-align: middle;
    }

    /* Varian Warna Soft UI */
    .bg-dot-success {
        background-color: #28c76f;
        box-shadow: 0 0 8px rgba(40, 199, 111, 0.4);
    }

    .bg-dot-primary {
        background-color: #666cff;
        box-shadow: 0 0 8px rgba(102, 108, 255, 0.4);
    }

    .bg-dot-warning {
        background-color: #ff9f43;
        box-shadow: 0 0 8px rgba(255, 159, 67, 0.4);
    }

    .bg-dot-danger {
        background-color: #ea5455;
        box-shadow: 0 0 8px rgba(234, 84, 85, 0.4);
    }

    .bg-dot-secondary {
        background-color: #82868b;
    }

    /* Animasi berkedip untuk status Aktif/Live */
    .badge-dot-animate {
        animation: pulse-dot 1.5s infinite;
    }

    @keyframes pulse-dot {
        0% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.4;
            transform: scale(1.2);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
