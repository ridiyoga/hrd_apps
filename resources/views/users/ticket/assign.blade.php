@extends('users.layouts.main')
@section('title')
APPS | ASSIGN Ticket ke PIC
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/select2.css') }}">
<style>
    .futuristic-header {
        padding: 28px 20px 18px;
    }

    .futuristic-icon {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(145deg, #7b2ff7, #9b6dff);
        color: white;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        box-shadow: 0 0 25px rgba(155, 109, 255, .6);
    }

    .header-back-btn {
        position: absolute;
        top: 16px;
        left: 16px;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #f3e8ff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7c3aed;
        font-size: 14px;
        text-decoration: none;
        transition: background .2s;
        z-index: 10;
    }

    .header-back-btn:hover {
        background: #ede9fe;
        color: #6d28d9;
    }

    /* SUMMARY */
    .summary-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin: 0 16px 16px;
    }

    @media (max-width: 480px) {
        .summary-strip {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .sum-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 14px;
        padding: 12px 8px;
        text-align: center;
    }

    .sum-card .sum-val {
        font-size: 20px;
        font-weight: 800;
        line-height: 1;
        color: #7c3aed;
    }

    .sum-card .sum-val.amber {
        color: #ba7517;
    }

    .sum-card .sum-val.blue {
        color: #185fa5;
    }

    .sum-card .sum-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: .4px;
        margin-top: 4px;
    }

    /* ─── SUMMARY KARYAWAN KPI (MODAL POPUP - OPSI A) ─── */
    .btn-kpi-modal-trigger {
        background: #fdfcff;
        border: 1.5px solid #ede9fe;
        color: #6d28d9;
        font-weight: 700;
        font-size: 11.5px;
        padding: 5px 12px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.06);
    }

    .btn-kpi-modal-trigger:hover {
        background: #f5f3ff;
        border-color: #c4b5fd;
        color: #5b21b6;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(124, 58, 237, 0.12);
    }

    .badge-kpi-pill {
        background: #ede9fe;
        color: #7c3aed;
        padding: 2px 7px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .summary-karyawan-kpi {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 10px;
        padding: 2px 0 6px;
    }

    .kpi-card {
        background: #ffffff;
        border: 1.2px solid #ede9fe;
        border-radius: 11px;
        padding: 9px 11px;
        box-sizing: border-box;
        min-width: 0;
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.03);
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .kpi-card:hover {
        border-color: #c4b5fd;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.08);
        transform: translateY(-1px);
    }

    .kpi-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        margin-bottom: 6px;
    }

    .karyawan-info {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        flex: 1;
    }

    .karyawan-meta {
        min-width: 0;
        flex: 1;
    }

    .karyawan-name {
        margin: 0;
        font-size: 11.5px;
        font-weight: 800;
        color: #1e1b4b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }

    .karyawan-role {
        font-size: 10px;
        color: #64748b;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .kpi-avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .kpi-avatar-img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid #ede9fe;
    }

    .kpi-avatar-initial {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7c3aed, #9333ea);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 11px;
        letter-spacing: 0.3px;
    }

    .kpi-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 6px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .kpi-status-badge.good {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .kpi-status-badge.warning {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .kpi-status-badge.bad {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .kpi-metrics-compact {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 4px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 8px;
        padding: 5px 4px;
        margin-bottom: 6px;
        text-align: center;
    }

    .kpi-metrics-compact .m-item {
        display: flex;
        flex-direction: column;
    }

    .kpi-metrics-compact .m-label {
        font-size: 8.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        line-height: 1;
        margin-bottom: 2px;
    }

    .kpi-metrics-compact .m-val {
        font-size: 11px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .kpi-speed-compact {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 6px;
    }

    .kpi-speed-bar {
        flex: 1;
        background: #e2e8f0;
        border-radius: 999px;
        height: 5px;
        overflow: hidden;
    }

    .kpi-speed-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.4s ease;
    }

    .kpi-speed-label {
        font-size: 9.5px;
        font-weight: 700;
        color: #64748b;
        white-space: nowrap;
    }

    .btn-kpi-detail {
        width: 100%;
        padding: 5px 10px;
        border: none;
        border-radius: 7px;
        background: linear-gradient(135deg, #7c3aed, #9333ea);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 10.5px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }

    .btn-kpi-detail:hover {
        background: linear-gradient(135deg, #6d28d9, #7e22ce);
        color: #ffffff !important;
    }

    .kpi-skeleton-card {
        background: #fff;
        border: 1.2px solid #ede9fe;
        border-radius: 11px;
        padding: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .kpi-empty {
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        color: #64748b;
        font-size: 11px;
        grid-column: 1 / -1;
    }

    /* Smartphone / Mobile Responsiveness */
    @media (max-width: 575px) {
        .summary-karyawan-kpi {
            grid-template-columns: 1fr;
            gap: 7px;
        }

        .kpi-card {
            padding: 8px 10px;
        }

        .kpi-metrics-compact {
            grid-template-columns: repeat(4, 1fr);
            padding: 4px 2px;
        }

        .kpi-metrics-compact .m-val {
            font-size: 10.5px;
        }
    }

    .form-select {
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 12px;
        color: #374151;
        background-color: #fdfcff;
        transition: all .2s ease;
        box-shadow: none;
    }

    .form-select:focus {
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, .12);
        background-color: #fff;
    }

    .form-select option {
        color: #374151;
        font-size: 12px;
    }

    /* FILTER TABS */
    .filter-tabs {
        display: flex;
        gap: 8px;
        padding: 0 16px 12px;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .filter-tabs::-webkit-scrollbar {
        display: none;
    }

    .filter-tab {
        flex-shrink: 0;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        border: 1.5px solid #ede9fe;
        background: #fff;
        color: #7c3aed;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }

    .filter-tab.active {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(124, 58, 237, .3);
    }

    /* SEARCH */
    .search-wrap {
        position: relative;
        margin: 0 16px 14px;
    }

    .search-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #a855f7;
        font-size: 13px;
        pointer-events: none;
    }

    .search-input {
        width: 100%;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 10px 14px 10px 40px;
        font-size: 13px;
        background: #fdfcff;
        color: #374151;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .search-input:focus {
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, .12);
    }

    .search-input::placeholder {
        color: #c4b5fd;
    }

    /* TICKET CARDS */
    .ticket-list {
        padding: 0 16px 100px;
    }

    /* ─── COLLAPSE GROUP ACCORDION ─── */
    .ticket-group {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        margin-bottom: 12px;
        overflow: hidden;
        transition: box-shadow .2s, border-color .2s;
    }
    .ticket-group:hover {
        border-color: #c4b5fd;
        box-shadow: 0 4px 16px rgba(124,58,237,.10);
    }
    .ticket-group-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        cursor: pointer;
        user-select: none;
        transition: background .15s;
        position: relative;
    }
    .ticket-group-header:hover {
        background: #faf8ff;
    }
    .ticket-group-header::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        border-radius: 4px 0 0 4px;
    }
    .ticket-group.all-assigned .ticket-group-header::before {
        background: linear-gradient(135deg, #185fa5, #378add);
    }
    .ticket-group.all-closed .ticket-group-header::before {
        background: linear-gradient(135deg, #16a34a, #22c55e);
    }
    .ticket-group-toggle {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #f5f3ff;
        border: 1.2px solid #ede9fe;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #7c3aed;
        flex-shrink: 0;
        transition: transform .25s ease, background .15s;
    }
    .ticket-group.collapsed .ticket-group-toggle {
        transform: rotate(-90deg);
    }
    .ticket-group-info {
        flex: 1;
        min-width: 0;
    }
    .ticket-group-title {
        font-family: monospace;
        font-size: 12.5px;
        font-weight: 700;
        color: #7c3aed;
        letter-spacing: .3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ticket-group-sub {
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }
    .ticket-group-badges {
        display: flex;
        gap: 5px;
        flex-shrink: 0;
        flex-wrap: wrap;
        align-items: center;
    }
    .tg-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        white-space: nowrap;
    }
    .tg-badge-count {
        background: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
    }
    .tg-badge-unassigned {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fde047;
    }
    .tg-badge-assigned {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .tg-badge-closed {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .ticket-group-body {
        border-top: 1px solid #f3f0ff;
        padding: 8px 10px 10px;
        background: #fcfbff;
        transition: max-height .3s ease;
        overflow: hidden;
    }
    .ticket-group.collapsed .ticket-group-body {
        display: none;
    }
    .ticket-sub-card {
        background: #fff;
        border: 1.2px solid #ede9fe;
        border-radius: 12px;
        padding: 10px 12px;
        margin-bottom: 6px;
        position: relative;
        overflow: hidden;
        transition: box-shadow .15s, border-color .15s;
    }
    .ticket-sub-card:last-child {
        margin-bottom: 0;
    }
    .ticket-sub-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #ddd6fe;
        border-radius: 3px 0 0 3px;
    }
    .ticket-sub-card.sub-assigned::before {
        background: #93c5fd;
    }
    .ticket-sub-card.sub-closed::before {
        background: #86efac;
    }
    .ticket-sub-card.sub-cancelled::before {
        background: #fca5a5;
    }
    .ticket-sub-card:hover {
        border-color: #c4b5fd;
        box-shadow: 0 3px 10px rgba(124,58,237,.08);
    }
    .tsc-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 6px;
        margin-bottom: 6px;
    }
    .tsc-nomor {
        font-family: monospace;
        font-size: 12px;
        font-weight: 700;
        color: #6d28d9;
    }
    .tsc-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 6px;
    }
    .tsc-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10.5px;
        color: #6b7280;
        background: #f9fafb;
        border: 1px solid #f0e8ff;
        border-radius: 6px;
        padding: 2px 6px;
    }
    .tsc-meta-item i { color: #a855f7; font-size: 9px; }
    .tsc-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }
    .tsc-requester-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        min-width: 0;
    }
    .tsc-actions {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-shrink: 0;
    }
    @media (max-width: 575px) {
        .ticket-group-header {
            flex-wrap: wrap;
            gap: 6px;
        }
        .ticket-group-badges {
            width: 100%;
            justify-content: flex-start;
        }
        .tsc-footer {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .tsc-actions {
            flex-direction: column;
            width: 100%;
        }
        .tsc-actions .btn-assign {
            width: 100%;
            border-radius: 9px;
            padding: 7px 12px;
        }
        .tsc-actions .btn-detail {
            width: 100%;
            height: auto;
            border-radius: 9px;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
    }
    /* ─── EXPAND ALL / COLLAPSE ALL BUTTONS ─── */
    .group-toggle-all {
        display: flex;
        justify-content: flex-end;
        gap: 6px;
        padding: 0 16px 8px;
    }
    .btn-toggle-all {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 10.5px;
        font-weight: 700;
        border: 1.2px solid #ede9fe;
        background: #fff;
        color: #7c3aed;
        cursor: pointer;
        transition: all .15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-toggle-all:hover {
        background: #f5f3ff;
        border-color: #c4b5fd;
    }

    .ticket-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        padding: 14px;
        margin-bottom: 10px;
        transition: box-shadow .2s, transform .15s, border-color .2s;
        text-decoration: none;
        display: block;
        position: relative;
        overflow: hidden;
    }

    .ticket-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        border-radius: 4px 0 0 4px;
    }

    .ticket-card.assigned::before {
        background: linear-gradient(135deg, #185fa5, #378add);
    }

    .ticket-card.closed::before {
        background: linear-gradient(135deg, #16a34a, #22c55e);
    }

    .ticket-card.cancelled::before {
        background: linear-gradient(135deg, #dc2626, #ef4444);
    }

    .ticket-card:hover {
        box-shadow: 0 8px 24px rgba(124, 58, 237, .14);
        transform: translateY(-2px);
        border-color: #c4b5fd;
        text-decoration: none;
    }

    .tc-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }

    .tc-nomor {
        font-family: monospace;
        font-size: 13px;
        font-weight: 700;
        color: #7c3aed;
        letter-spacing: .3px;
    }

    .tc-status-badge {
        font-size: 10.5px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .badge-unassigned {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fde047;
    }

    .badge-assigned {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .badge-in-progress {
        background: #ecfeff;
        color: #0e7490;
        border: 1px solid #a5f3fc;
    }

    .badge-waiting {
        background: #fefce8;
        color: #a16207;
        border: 1px solid #fde047;
    }

    .badge-feedback {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
    }

    .badge-closed {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-cancelled {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    /* Environment Badges */
    .badge-env-epicor {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }
    .badge-env-accurate-sp {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }
    .badge-env-accurate-aja {
        background: #f0fdfa;
        color: #0f766e;
        border: 1px solid #99f6e4;
    }

    .tc-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 8px;
    }

    .tc-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        color: #6b7280;
        background: #f9fafb;
        border: 1px solid #f0e8ff;
        border-radius: 8px;
        padding: 3px 8px;
    }

    .tc-meta-item i {
        color: #a855f7;
        font-size: 10px;
    }

    .tc-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 10px;
    }

    .tc-requester-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }

    .tc-requester {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .tc-pic-requester {
        border-left: 1.5px solid #ede9fe;
        padding-left: 10px;
    }

    .tc-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: #7c3aed;
        flex-shrink: 0;
    }

    .tc-avatar.pic-avatar {
        background: linear-gradient(135deg, #e6f1fb, #b5d4f4);
        color: #185fa5;
    }

    .tc-name {
        font-size: 11px;
        font-weight: 600;
        color: #374151;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tc-date {
        font-size: 10px;
        color: #9ca3af;
    }

    /* BUTTONS */
    .tc-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .btn-text-mobile {
        display: none;
    }

    .btn-assign {
        flex-shrink: 0;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        border: none;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        cursor: pointer;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        box-shadow: 0 3px 10px rgba(124, 58, 237, .25);
    }

    .btn-assign:hover {
        box-shadow: 0 5px 16px rgba(124, 58, 237, .35);
        transform: translateY(-1px);
    }

    .btn-assign:active {
        transform: scale(.96);
    }

    .btn-reassign {
        background: linear-gradient(135deg, #185fa5, #378add);
        box-shadow: 0 3px 10px rgba(24, 95, 165, .25);
    }

    .btn-reassign:hover {
        box-shadow: 0 5px 16px rgba(24, 95, 165, .35);
    }

    .btn-detail {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1.5px solid #ede9fe;
        background: #faf9ff;
        color: #7c3aed;
        cursor: pointer;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    .btn-detail:hover {
        background: #ede9fe;
        border-color: #c4b5fd;
        transform: scale(1.08);
    }

    /* ─── SMARTPHONE / MOBILE ACTION BUTTONS LIST ─── */
    @media (max-width: 575px) {
        .tc-footer {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            margin-top: 10px;
            padding-top: 9px;
            border-top: 1px dashed #f1f5f9;
        }

        .tc-requester-group {
            width: 100%;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tc-actions {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 6px;
        }

        .btn-text-mobile {
            display: inline;
        }

        .tc-actions .btn-assign {
            width: 100%;
            border-radius: 9px;
            padding: 8px 12px;
            font-size: 11.5px;
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.2);
            order: 1;
        }

        .tc-actions .btn-detail {
            width: 100%;
            height: auto;
            border-radius: 9px;
            padding: 7px 12px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transform: none !important;
        }

        .tc-actions .btn-open-detail {
            background: #f5f3ff;
            border: 1.2px solid #ddd6fe;
            color: #6d28d9;
            order: 2;
        }

        .tc-actions .btn-tc-pdf {
            background: #fff1f2;
            border: 1.2px solid #fecdd3;
            color: #be123c !important;
            order: 3;
        }
    }

    /* EMPTY STATE */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #c4b5fd;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 12px;
        display: block;
    }

    .empty-state p {
        font-size: 13px;
        color: #9ca3af;
        margin: 0;
    }

    .empty-state small {
        font-size: 11px;
        color: #d1d5db;
    }

    /* SKELETON */
    .skeleton {
        display: inline-block;
        background: linear-gradient(90deg, #ede9fe 25%, #f5f3ff 37%, #ede9fe 63%);
        background-size: 400% 100%;
        animation: skPulse 1.4s ease-in-out infinite;
        border-radius: 6px;
        width: 100%;
    }

    @keyframes skPulse {

        0%,
        100% {
            opacity: .5
        }

        50% {
            opacity: 1
        }
    }

    .skeleton-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        padding: 14px;
        margin-bottom: 10px;
    }

    /* FAB REFRESH */
    .fab-refresh {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        border: none;
        font-size: 16px;
        box-shadow: 0 6px 18px rgba(124, 58, 237, .4);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
        transition: transform .2s;
    }

    .fab-refresh:active {
        transform: scale(.92) rotate(180deg);
    }

    /* ===================== MODAL ASSIGN ===================== */
    .modal-assign .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(124, 58, 237, .15);
        overflow: hidden;
    }

    .modal-assign .modal-header {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        border-bottom: none;
        padding: 20px 20px 16px;
    }

    .modal-assign .modal-header .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: #fff;
    }

    .modal-assign .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: .8;
    }

    .modal-assign .modal-body {
        padding: 20px;
    }

    .ticket-info-box {
        background: #f5f3ff;
        border: 1.5px solid #c4b5fd;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 16px;
    }

    .ticket-info-box .ti-nomor {
        font-size: 13px;
        font-weight: 800;
        color: #6b21a8;
        margin-bottom: 4px;
    }

    .ticket-info-box .ti-detail {
        font-size: 11px;
        color: #6b7280;
    }

    .pic-select-label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pic-select-label i {
        color: #a855f7;
    }

    .pic-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        max-height: 280px;
        overflow-y: auto;
        padding: 2px;
        scrollbar-width: thin;
        scrollbar-color: #c4b5fd transparent;
    }

    .pic-grid::-webkit-scrollbar {
        width: 4px;
    }

    .pic-grid::-webkit-scrollbar-thumb {
        background: #c4b5fd;
        border-radius: 4px;
    }

    .pic-card {
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 10px;
        cursor: pointer;
        transition: all .2s;
        text-align: center;
        position: relative;
        background: #fff;
    }

    .pic-card:hover {
        border-color: #a855f7;
        box-shadow: 0 4px 12px rgba(168, 85, 247, .12);
        transform: translateY(-1px);
    }

    .pic-card.selected {
        border-color: #7c3aed;
        background: #f5f3ff;
        box-shadow: 0 4px 14px rgba(124, 58, 237, .18);
    }

    .pic-card .pic-check {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #7c3aed;
        color: #fff;
        font-size: 10px;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .pic-card.selected .pic-check {
        display: flex;
    }

    .pic-card .pic-av {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        color: #7c3aed;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 6px;
    }

    .pic-card .pic-name {
        font-size: 11px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pic-card .pic-role {
        font-size: 10px;
        color: #9ca3af;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pic-card .pic-load {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-top: 4px;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 50px;
    }

    .pic-load.load-low {
        background: #f5f3ff;
        color: #7c3aed;
    }

    .pic-load.load-mid {
        background: #faeeda;
        color: #854f0b;
    }

    .pic-load.load-high {
        background: #fcebeb;
        color: #a32d2d;
    }

    .note-textarea {
        width: 100%;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 12px;
        color: #374151;
        resize: none;
        outline: none;
        background: #fdfcff;
        transition: border-color .2s;
        margin-top: 14px;
    }

    .note-textarea:focus {
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, .1);
    }

    .note-textarea::placeholder {
        color: #c4b5fd;
    }

    .modal-assign .modal-footer {
        padding: 12px 20px 20px;
        border-top: 1px solid #ede9fe;
        gap: 8px;
    }

    .btn-confirm-assign {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 10px 24px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .2s;
        flex: 1;
        justify-content: center;
    }

    .btn-confirm-assign:hover {
        box-shadow: 0 6px 16px rgba(124, 58, 237, .3);
        transform: translateY(-1px);
        color: #fff;
    }

    .btn-confirm-assign:disabled {
        opacity: .5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-cancel-modal {
        background: #f3f4f6;
        color: #6b7280;
        border: none;
        border-radius: 12px;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 600;
        transition: all .2s;
    }

    .btn-cancel-modal:hover {
        background: #e5e7eb;
        color: #374151;
    }

    .pic-assigned-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #e6f1fb;
        color: #185fa5;
        border: 1px solid #b5d4f4;
        border-radius: 50px;
        padding: 3px 10px;
        font-size: 10px;
        font-weight: 700;
    }

    .pic-search-wrap {
        position: relative;
        margin-bottom: 10px;
    }

    .pic-search-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #a855f7;
        font-size: 12px;
        pointer-events: none;
    }

    .pic-search-input {
        width: 100%;
        border: 1.5px solid #ede9fe;
        border-radius: 10px;
        padding: 8px 12px 8px 34px;
        font-size: 12px;
        background: #fdfcff;
        color: #374151;
        outline: none;
    }

    .pic-search-input:focus {
        border-color: #a855f7;
    }

    .pic-search-input::placeholder {
        color: #c4b5fd;
    }

    /* ===================== MODAL DETAIL ===================== */
    .modal-detail .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(124, 58, 237, .15);
        overflow: hidden;
    }

    .modal-detail .modal-header {
        background: linear-gradient(135deg, #185fa5, #378add);
        color: #fff;
        border-bottom: none;
        padding: 20px 20px 16px;
    }

    .modal-detail .modal-header .modal-title {
        font-size: 15px;
        font-weight: 700;
        color: #fff;
    }

    .modal-detail .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: .8;
    }

    .modal-detail .modal-body {
        padding: 0;
    }

    /* Detail sections */
    .detail-section {
        padding: 16px 18px;
        border-bottom: 1px solid #f0e8ff;
    }

    .detail-section:last-child {
        border-bottom: none;
    }

    .detail-section-title {
        font-size: 10px;
        font-weight: 700;
        color: #a855f7;
        text-transform: uppercase;
        letter-spacing: .6px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Info row */
    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 6px;
    }

    .info-row-label {
        font-size: 10px;
        color: #9ca3af;
        font-weight: 600;
        min-width: 80px;
        flex-shrink: 0;
        padding-top: 1px;
    }

    .info-row-val {
        font-size: 12px;
        color: #1f2937;
        font-weight: 600;
        flex: 1;
        word-break: break-word;
    }

    /* Status pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 700;
    }

    .status-pill.approved {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .status-pill.pending {
        background: #fff8e1;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .status-pill.rejected {
        background: #fff1f2;
        color: #9f1239;
        border: 1px solid #fecdd3;
    }
    .status-pill.waiting_assign {
        background: #fff1f2;
        color: #9f6e12;
        border: 1px solid #fecdd3;
    }

    .status-pill.ready {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    /* Signature grid */
    .sig-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 8px;
    }

    .sig-box {
        background: #faf9ff;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 10px;
        text-align: center;
    }

    .sig-box-label {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        color: #a855f7;
        letter-spacing: .5px;
        margin-bottom: 6px;
    }

    .sig-box-name {
        font-size: 10px;
        font-weight: 700;
        color: #374151;
        margin-top: 6px;
        margin-bottom: 2px;
    }

    .sig-box-date {
        font-size: 9px;
        color: #9ca3af;
    }

    .sig-img-wrap {
        background: #fff;
        border: 1px solid #f0e8ff;
        border-radius: 8px;
        min-height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .sig-img-wrap img {
        max-height: 70px;
        max-width: 100%;
        object-fit: contain;
    }

    .sig-img-wrap .sig-empty {
        font-size: 10px;
        color: #c4b5fd;
        font-style: italic;
        padding: 8px;
    }

    .sig-box.done {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .sig-box.done .sig-box-label {
        color: #166534;
    }

    .sig-box.pending-sig {
        border-color: #fde68a;
        background: #fffbeb;
    }

    .sig-box.pending-sig .sig-box-label {
        color: #b45309;
    }

    /* Detail perubahan inside modal */
    .change-accordion-item {
        background: #ffffff;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }
    .change-accordion-item:hover {
        border-color: #c4b5fd;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.06);
    }
    .change-accordion-header {
        display: flex;
        align-items: center;
        padding: 10px 14px;
        cursor: pointer;
        user-select: none;
        background: #fdfcff;
        gap: 12px;
        transition: background 0.2s ease;
    }
    .change-accordion-header:hover {
        background: #f8f4ff;
    }
    .change-accordion-header[aria-expanded="true"] {
        background: #f5f0ff;
        border-bottom: 1px solid #ede9fe;
    }
    .change-accordion-chevron {
        color: #7c3aed;
        font-size: 11px;
        transition: transform 0.25s ease;
        margin-left: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #f3e8ff;
        flex-shrink: 0;
    }
    .change-accordion-header[aria-expanded="true"] .change-accordion-chevron {
        transform: rotate(180deg);
        background: #7c3aed;
        color: #ffffff;
    }
    .change-accordion-body {
        padding: 12px 14px;
        background: #faf8ff;
    }
    .change-card-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        flex-shrink: 0;
    }
    .change-card-info {
        flex: 1;
        min-width: 0;
    }
    .change-card-info .nomor {
        font-size: 12px;
        font-weight: 700;
        color: #6b21a8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .change-card-info .alasan {
        font-size: 11px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .subdetails-wrap {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .subdetail-card {
        background: #ffffff;
        border: 1px solid #ede9fe;
        border-radius: 10px;
        padding: 12px;
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.03);
    }
    .subdetail-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .subdetail-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .subdetail-field-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .subdetail-field-val {
        font-size: 12px;
        color: #1f2937;
        word-break: break-word;
        white-space: pre-wrap;
        user-select: text;
        padding: 2px 0;
    }
    .subdetail-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        gap: 8px;
    }
    .subdetail-line-badge {
        font-size: 11px;
        font-weight: 700;
        background: #ede9fe;
        color: #6b21a8;
        padding: 3px 10px;
        border-radius: 6px;
        display: inline-block;
        width: fit-content;
    }
    .subdetail-reason {
        font-size: 11px;
        font-weight: 600;
        color: #4b5563;
        text-align: right;
    }
    .subdetail-diff-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .diff-box {
        border-radius: 6px;
        padding: 8px 10px;
        font-size: 11.5px;
    }
    .diff-before {
        background: #fef2f2;
        border: 1px solid #fee2e2;
    }
    .diff-before .diff-label {
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #dc2626;
        margin-bottom: 4px;
        letter-spacing: 0.3px;
    }
    .diff-after {
        background: #f0fdf4;
        border: 1px solid #dcfce7;
    }
    .diff-after .diff-label {
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #16a34a;
        margin-bottom: 4px;
        letter-spacing: 0.3px;
    }
    .diff-content {
        color: #1f2937;
        word-break: break-word;
        white-space: pre-wrap;
        line-height: 1.35;
    }

    /* Loading skeleton inside modal */
    .detail-skeleton {
        padding: 20px;
    }

    /* Modal footer */
    .modal-detail .modal-footer {
        padding: 12px 18px 16px;
        border-top: 1px solid #ede9fe;
        gap: 8px;
    }

    .btn-to-assign {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .2s;
        flex: 1;
        justify-content: center;
    }

    .btn-to-assign:hover {
        box-shadow: 0 6px 16px rgba(124, 58, 237, .3);
        transform: translateY(-1px);
        color: #fff;
    }

    .btn-to-assign.reassign {
        background: linear-gradient(135deg, #185fa5, #378add);
    }

    .btn-close-detail {
        background: #f3f4f6;
        color: #6b7280;
        border: none;
        border-radius: 12px;
        padding: 10px 18px;
        font-size: 12px;
        font-weight: 600;
        transition: all .2s;
    }

    .btn-close-detail:hover {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-export-pdf {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 10px 16px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .2s;
        justify-content: center;
        text-decoration: none;
    }

    .btn-export-pdf:hover {
        box-shadow: 0 6px 16px rgba(239, 68, 68, .3);
        transform: translateY(-1px);
        color: #fff;
    }

    /* ===== PRIORITY GRID ===== */
    .priority-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 6px;
    }

    .priority-opt {
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 10px 12px;
        cursor: pointer;
        transition: all .18s;
        background: #fdfcff;
        display: flex;
        align-items: center;
        gap: 10px;
        user-select: none;
    }

    .priority-opt:hover {
        border-color: #c4b5fd;
        background: #faf8ff;
    }

    .priority-opt.selected {
        border-width: 2px;
    }

    /* Warna per level */
    .priority-opt.p01.selected {
        border-color: #e24b4a;
        background: #fff5f5;
    }

    .priority-opt.p02.selected {
        border-color: #ef9f27;
        background: #fffbf3;
    }

    .priority-opt.p03.selected {
        border-color: #7c3aed;
        background: #f5f3ff;
    }

    .priority-opt.p04.selected {
        border-color: #1d9e75;
        background: #f0fdf8;
    }

    .popt-dot {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 14px;
    }

    .p01 .popt-dot {
        background: #fee2e2;
        color: #b91c1c;
    }

    .p02 .popt-dot {
        background: #ffedd5;
        color: #c2410c;
    }

    .p03 .popt-dot {
        background: #ede9fe;
        color: #7c3aed;
    }

    .p04 .popt-dot {
        background: #dcfce7;
        color: #15803d;
    }

    .popt-body {
        flex: 1;
        min-width: 0;
    }

    .popt-title {
        font-size: 12px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .popt-sla {
        font-size: 10px;
        color: #9ca3af;
    }

    .popt-check {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 1.5px solid #e5e7eb;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all .15s;
    }

    .priority-opt.selected .popt-check {
        border-color: transparent;
    }

    .priority-opt.p01.selected .popt-check {
        background: #e24b4a;
    }

    .priority-opt.p02.selected .popt-check {
        background: #ef9f27;
    }

    .priority-opt.p03.selected .popt-check {
        background: #7c3aed;
    }

    .priority-opt.p04.selected .popt-check {
        background: #1d9e75;
    }

    .popt-check-inner {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #fff;
        opacity: 0;
        transition: opacity .15s;
    }

    .priority-opt.selected .popt-check-inner {
        opacity: 1;
    }

    /* SLA info pill */
    .sla-info-pill {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
        border: 1px solid;
        margin-top: 6px;
    }

    .sla-info-pill.p01 {
        background: #fff5f5;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .sla-info-pill.p02 {
        background: #fffbf3;
        border-color: #fde68a;
        color: #854f0b;
    }

    .sla-info-pill.p03 {
        background: #f5f3ff;
        border-color: #c4b5fd;
        color: #6b21a8;
    }

    .sla-info-pill.p04 {
        background: #f0fdf8;
        border-color: #a7f3d0;
        color: #065f46;
    }

    /* ── Priority badge (card & modal) ── */
    .prio-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 3px 8px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 700;
    }

    .prio-badge.p01 {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    .prio-badge.p02 {
        background: #ffedd5;
        color: #c2410c;
        border: 1px solid #fdba74;
    }

    .prio-badge.p03 {
        background: #ede9fe;
        color: #6b21a8;
        border: 1px solid #c4b5fd;
    }

    .prio-badge.p04 {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    /* ── Priority detail pill (modal) ── */
    .prio-detail-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid;
    }

    .prio-detail-pill .prio-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .prio-detail-pill.p01 {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
    }

    .prio-detail-pill.p02 {
        background: #ffedd5;
        color: #9a3412;
        border-color: #fdba74;
    }

    .prio-detail-pill.p03 {
        background: #ede9fe;
        color: #5b21b6;
        border-color: #c4b5fd;
    }

    .prio-detail-pill.p04 {
        background: #dcfce7;
        color: #15803d;
        border-color: #86efac;
    }

    .prio-detail-pill.p01 .prio-dot {
        background: #dc2626;
    }

    .prio-detail-pill.p02 .prio-dot {
        background: #ea580c;
    }

    .prio-detail-pill.p03 .prio-dot {
        background: #7c3aed;
    }

    .prio-detail-pill.p04 .prio-dot {
        background: #16a34a;
    }

    /* ── SLA strip (modal) ── */
    .sla-strip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 700;
        border: 1px solid;
    }

    .sla-strip.p01 {
        background: #fff5f5;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .sla-strip.p02 {
        background: #fffbf3;
        border-color: #fde68a;
        color: #854f0b;
    }

    .sla-strip.p03 {
        background: #f5f3ff;
        border-color: #c4b5fd;
        color: #6b21a8;
    }

    .sla-strip.p04 {
        background: #f0fdf8;
        border-color: #a7f3d0;
        color: #065f46;
    }

    /* ── Lampiran ── */
    .lampiran-box {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f5f3ff;
        border: 1.5px solid #c4b5fd;
        border-radius: 10px;
        padding: 8px 12px;
        text-decoration: none;
        transition: all .18s;
        max-width: 100%;
    }

    .lampiran-box:hover {
        background: #ede9fe;
        border-color: #a855f7;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(124, 58, 237, .12);
    }

    .lampiran-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        font-size: 14px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lampiran-info {
        min-width: 0;
    }

    .lampiran-name {
        font-size: 11px;
        font-weight: 700;
        color: #4c1d95;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .lampiran-meta {
        font-size: 10px;
        color: #9ca3af;
    }

    .lampiran-none {
        font-size: 11px;
        color: #c4b5fd;
        font-style: italic;
    }

    /* ── KPI block (detail modal) ── */
    .kpi-box {
        background: #faf9ff;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 10px 12px;
        margin-top: 8px;
    }

    .kpi-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        padding: 3px 0;
    }

    .kpi-row .kpi-label {
        color: #9ca3af;
        font-weight: 600;
    }

    .kpi-row .kpi-val {
        color: #1f2937;
        font-weight: 700;
    }

    .kpi-row .kpi-val.overrun {
        color: #b91c1c;
    }

    .kpi-row .kpi-val.ontime {
        color: #15803d;
    }

    .kpi-overrun-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
        border-radius: 50px;
        padding: 2px 9px;
        font-size: 10px;
        font-weight: 700;
    }

    .kpi-ontime-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
        border-radius: 50px;
        padding: 2px 9px;
        font-size: 10px;
        font-weight: 700;
    }

    /* ── SUBDETAIL ACCORDION (inside modal change-accordion-body) ── */
    .subdetail-accordion-item {
        border: 1.5px solid #ede9fe;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 6px;
        background: #ffffff;
        transition: border-color 0.2s ease;
    }
    .subdetail-accordion-item:hover {
        border-color: #c4b5fd;
    }
    .subdetail-accordion-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px;
        cursor: pointer;
        user-select: none;
        background: #fdfcff;
        gap: 8px;
        transition: background 0.15s ease;
    }
    .subdetail-accordion-header:hover {
        background: #f5f0ff;
    }
    .subdetail-accordion-header[aria-expanded="true"] {
        background: #f3e8ff;
        border-bottom: 1px solid #ede9fe;
    }
    .subdetail-chevron {
        color: #7c3aed;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ede9fe;
        flex-shrink: 0;
        transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
    }
    .subdetail-accordion-header[aria-expanded="true"] .subdetail-chevron {
        transform: rotate(180deg);
        background: #7c3aed;
        color: #ffffff;
    }
    .subdetail-accordion-body {
        padding: 10px 12px;
        background: #faf9ff;
    }
    .field-empty-sd {
        color: #c4b5fd;
        font-style: italic;
        font-size: 11px;
    }
    .sd-field-view {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 11px;
        color: #374151;
        word-break: break-word;
        white-space: pre-wrap;
        line-height: 1.4;
    }
    .sd-field-before {
        background: #fef2f2;
        border-color: #fee2e2;
    }
    .sd-field-after {
        background: #f0fdf4;
        border-color: #dcfce7;
    }

    .line-cancel-box{background:#fef2f2;border:1px dashed #fca5a5;border-radius:8px;padding:8px 10px;font-size:11.5px;margin-bottom:8px}
    .line-cancel-box .lcb-title{font-weight:700;color:#b91c1c}
    .line-cancel-box .lcb-reason{margin-top:3px;color:#7f1d1d;white-space:pre-wrap;word-break:break-word}
    .is-line-cancelled{opacity:.72}
    .is-line-cancelled .diff-content,
    .is-line-cancelled .subdetail-field-val,
    .is-line-cancelled .field-readonly{text-decoration:line-through}
</style>
@endsection

@section('content_top')
<div class="card" style="position:relative; padding-bottom: 16px;">

    <a href="{{ url()->previous() }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <h5 class="mt-3 mb-1" style="color:#7c3aed; font-size:20px;">Assign Ticket</h5>
        <small class="text-muted">Tugaskan PIC untuk mengerjakan ticket</small>
    </div>

    <div class="summary-strip">
        <div class="sum-card">
            <div class="sum-val" id="sum-total">
                <span class="skeleton" style="height:20px; width:32px; display:inline-block;"></span>
            </div>
            <div class="sum-label">Total</div>
        </div>
        <div class="sum-card">
            <div class="sum-val amber" id="sum-unassigned">
                <span class="skeleton" style="height:20px; width:32px; display:inline-block;"></span>
            </div>
            <div class="sum-label">Belum Assign</div>
        </div>
        <div class="sum-card">
            <div class="sum-val text-info" id="sum-inprogress" style="color:#0891b2;">
                <span class="skeleton" style="height:20px; width:32px; display:inline-block;"></span>
            </div>
            <div class="sum-label">Dikerjakan</div>
        </div>
        <div class="sum-card">
            <div class="sum-val text-success" id="sum-closed" style="color:#16a34a;">
                <span class="skeleton" style="height:20px; width:32px; display:inline-block;"></span>
            </div>
            <div class="sum-label">Selesai</div>
        </div>
    </div>

    <!-- Tombol Akses Cepat Pantau Beban & KPI PIC (Opsi A) -->
    <div class="px-3 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small fw-bold" style="font-size:11.5px;">
            <i class="fa-solid fa-list-check me-1 text-primary"></i> Daftar Penugasan Tiket
        </div>
        <button type="button" class="btn-kpi-modal-trigger" data-bs-toggle="modal" data-bs-target="#modalKpiPic">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Beban &amp; KPI PIC</span>
            <span class="badge-kpi-pill" id="kpi-btn-count">0 PIC</span>
        </button>
    </div>


    <div class="search-wrap">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" class="search-input" id="search-input"
            placeholder="Cari nomor ticket, pengaju, menu...">
    </div>

    <div class="filter-tabs">
        <div class="filter-tab" data-filter="all">Semua</div>
        <div class="filter-tab active" data-filter="unassigned">
            <i class="fa-solid fa-clock me-1"></i>Belum Assign
        </div>
        <div class="filter-tab" data-filter="assigned">
            <i class="fa-solid fa-user-check me-1"></i>Ditugaskan
        </div>
        <div class="filter-tab" data-filter="in_progress">
            <i class="fa-solid fa-spinner me-1"></i>Dikerjakan
        </div>
        <div class="filter-tab" data-filter="feedback">
            <i class="fa-solid fa-comment-dots me-1"></i>Feedback / Revisi
        </div>
        <div class="filter-tab" data-filter="closed">
            <i class="fa-solid fa-circle-check me-1"></i>Selesai
        </div>
        <div class="filter-tab" data-filter="cancelled">
            <i class="fa-solid fa-ban me-1"></i>Dibatalkan
        </div>
    </div>

    <div class="group-toggle-all" id="group-toggle-all" style="display:none;">
        <button class="btn-toggle-all" id="btn-expand-all"><i class="fa-solid fa-angles-down"></i> Buka Semua</button>
        <button class="btn-toggle-all" id="btn-collapse-all"><i class="fa-solid fa-angles-up"></i> Tutup Semua</button>
    </div>

    <div class="ticket-list" id="ticket-list">
        @for($sk = 0; $sk < 4; $sk++)
            <div class="skeleton-card">
            <div class="d-flex justify-content-between mb-2">
                <span class="skeleton" style="height:14px; width:60%; display:inline-block;"></span>
                <span class="skeleton" style="height:14px; width:20%; display:inline-block;"></span>
            </div>
            <div class="d-flex gap-2 mb-2">
                <span class="skeleton" style="height:22px; width:90px; display:inline-block; border-radius:8px;"></span>
                <span class="skeleton" style="height:22px; width:80px; display:inline-block; border-radius:8px;"></span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="skeleton" style="height:28px; width:28px; border-radius:50%; display:inline-block;"></span>
                <span class="skeleton" style="height:12px; width:40%; display:inline-block;"></span>
            </div>
    </div>
    @endfor
</div>
</div>

<button class="fab-refresh" id="btn-refresh" title="Refresh">
    <i class="fa-solid fa-rotate-right"></i>
</button>
@endsection

@section('content')
<!-- MODAL BEBAN KERJA & KPI PIC (OPSI A) -->
<div class="modal fade" id="modalKpiPic" tabindex="-1" aria-labelledby="modalKpiPicLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content" style="border:none; border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #7c3aed, #9333ea); color:#fff; border-bottom:none; padding:14px 18px;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:34px; height:34px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fa-solid fa-users-gear text-white" style="font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="modalKpiPicLabel" style="font-size:14px;">Ringkasan Beban &amp; KPI PIC</h6>
                        <small style="color:rgba(255,255,255,0.85); font-size:11px;">Pantau beban kerja aktif &amp; performa kecepatan SLA PIC</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="background: #f8fafc; max-height: 75vh;">
                <div class="summary-karyawan-kpi" id="kpiContainer">
                    {{-- Diisi otomatis lewat AJAX --}}
                    <div class="kpi-skeleton-card">
                        <span class="skeleton" style="height:32px; width:32px; border-radius:50%; display:inline-block;"></span>
                        <div style="flex:1;">
                            <span class="skeleton" style="height:12px; width:50%; display:block; margin-bottom:4px;"></span>
                            <span class="skeleton" style="height:8px; width:30%; display:block;"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between py-2 px-3" style="background:#fff; border-top:1px solid #e5e7eb;">
                <span class="text-muted small" style="font-size:11px;">
                    <i class="fa-solid fa-circle-info me-1 text-primary"></i> Klik 'Detail KPI' untuk melihat histori pengerjaan PIC.
                </span>
                <button type="button" class="btn btn-secondary btn-sm rounded-3 fw-bold" data-bs-dismiss="modal" style="font-size:11.5px; padding:6px 14px;">
                    <i class="fa-solid fa-xmark me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-detail" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <div class="modal-title" id="modalDetailLabel">
                        <i class="fa-solid fa-file-lines me-2"></i>Detail Ticket
                    </div>
                    <div style="font-size:11px; opacity:.85; margin-top:3px;" id="detail-nomor-sub">-</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-detail-body">
                <div class="detail-skeleton">
                    <div class="d-flex flex-column gap-3">
                        <span class="skeleton" style="height:14px; width:80%; display:block;"></span>
                        <span class="skeleton" style="height:14px; width:60%; display:block;"></span>
                        <span class="skeleton" style="height:80px; width:100%; display:block; border-radius:12px;"></span>
                        <span class="skeleton" style="height:120px; width:100%; display:block; border-radius:12px;"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="#" target="_blank" class="btn-export-pdf" id="btn-detail-export-pdf" style="display:none;" title="Download / Print PDF">
                    <i class="fa-solid fa-file-pdf"></i> Export PDF
                </a>
                <button type="button" class="btn-close-detail" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn-to-assign" id="btn-detail-to-assign" style="display:none;">
                    <i class="fa-solid fa-user-plus"></i> Assign PIC
                </button>
                <button type="button" class="btn-to-assign reassign" id="btn-detail-to-reassign" style="display:none;">
                    <i class="fa-solid fa-pen"></i> Ubah PIC
                </button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade modal-assign" id="modalAssign" tabindex="-1" aria-labelledby="modalAssignLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div class="modal-title" id="modalAssignLabel">
                        <i class="fa-solid fa-user-plus me-2"></i>Assign PIC
                    </div>
                    <div style="font-size:11px; opacity:.85; margin-top:3px;" id="modal-ticket-nomor">-</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ticket-info-box" id="modal-ticket-info">
                    <div class="ti-nomor" id="modal-ti-nomor">-</div>
                    <div class="ti-detail" id="modal-ti-detail">-</div>
                </div>
                <div id="modal-current-pic" style="margin-bottom:12px; display:none;">
                    <div class="pic-select-label">
                        <i class="fa-solid fa-user-tie"></i> PIC Saat Ini
                    </div>
                    <div id="modal-current-pic-tag"></div>
                </div>
                <div class="pic-select-label">
                    <i class="fa-solid fa-users"></i> Pilih PIC
                </div>
                <div class="pic-search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="pic-search-input" id="pic-search" placeholder="Cari nama PIC...">
                </div>
                <div class="pic-grid" id="pic-grid">
                    <div class="text-center text-muted py-3" style="font-size:12px; grid-column:span 2;">
                        <i class="fa-solid fa-spinner fa-spin me-1"></i> Memuat daftar PIC...
                    </div>
                </div>
                <div class="mt-3">
                    <div class="pic-select-label">
                        <i class="fa-solid fa-triangle-exclamation"></i> Tingkat Kesulitan Ticket
                    </div>
                    <div class="priority-grid" id="prio-grid">
                        <div class="text-center text-muted py-3" style="font-size:12px; grid-column:span 2;">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Memuat tingkat kesulitan...
                        </div>
                    </div>
                    <div class="sla-info-pill" id="sla-info-pill" style="display:none;"></div>
                </div>
                <textarea class="note-textarea" id="assign-note" rows="3"
                    placeholder="Catatan untuk PIC (opsional)..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-confirm-assign" id="btn-confirm-assign" disabled>
                    <i class="fa-solid fa-paper-plane"></i> Assign Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();"
                class="btn btn-sm btn-danger light">
                <i class="fa-solid fa-right-from-bracket"></i>&nbsp;Logout
            </a>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas">Batal</a>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });

    function getEnvBadge(kategori, name, nomor) {
        let kat = Number(kategori);
        if (!kat || isNaN(kat) || kat === 0) {
            if (nomor && nomor.includes('AOL-AJA')) kat = 3;
            else if (nomor && (nomor.includes('AOL-SP') || nomor.includes('AOL-SPS'))) kat = 2;
            else if (nomor && (nomor.includes('EPIC-SPS') || nomor.includes('EPIC'))) kat = 1;
            else kat = 1;
        }
        if (nomor && nomor.includes('AOL-AJA')) {
            kat = 3;
        } else if (nomor && (nomor.includes('AOL-SP') || nomor.includes('AOL-SPS'))) {
            kat = 2;
        } else if (nomor && nomor.includes('EPIC')) {
            kat = 1;
        }
        let cls = 'badge-env-epicor';
        let icon = 'fa-database';
        let label = 'Epicor SPS';
        if (kat === 2) {
            cls = 'badge-env-accurate-sp';
            icon = 'fa-bolt';
            label = 'AOL SP';
        } else if (kat === 3) {
            cls = 'badge-env-accurate-aja';
            icon = 'fa-network-wired';
            label = 'AOL AJA';
        }
        return `<span class="badge ${cls}" style="font-size:10px; font-weight:700; padding:2px 7px; border-radius:6px; display:inline-flex; align-items:center; gap:3px;"><i class="fa-solid ${icon}"></i>${label}</span>`;
    }

    $(document).ready(function() {
                const MENU_ID = '{{ $menu_id }}';
                const BASE_ASSIGN = "{{ url('approval/ticket/assign') }}";
                const ASSIGN_URL = "{{ route('approval.ticket.assign.store') }}";
                const GET_LIST_URL = BASE_ASSIGN + '/' + MENU_ID + '/list';
                const GET_PIC_URL = BASE_ASSIGN + '/' + MENU_ID + '/pic';
                const GET_DETAIL_URL = BASE_ASSIGN + '/' + MENU_ID + '/detail/'; // + ticketId
                const DIFFICULTY_URL = "{{ url('approval/ticket/kpi/difficulty-master') }}";
                const KPI_URL = "{{ url('approval/ticket/kpi') }}";
                const KPI_DASHBOARD_URL = "{{ url('approval/ticket/kpi/dashboard') }}";
                let allTickets = [];
                let allPic = [];
                let activeFilter = 'unassigned';
                let searchQuery = '';
                let selectedPicId = null;
                let currentTicket = null;
                let difficultyMap = {};
                const difficultyMeta = {
                    P01: {
                        icon: 'fa-circle-radiation',
                        cls: 'p01',
                        emoji: '🔴'
                    },
                    P02: {
                        icon: 'fa-fire',
                        cls: 'p02',
                        emoji: '🟠'
                    },
                    P03: {
                        icon: 'fa-bolt',
                        cls: 'p03',
                        emoji: '🟣'
                    },
                    P04: {
                        icon: 'fa-leaf',
                        cls: 'p04',
                        emoji: '🟢'
                    },
                };
                function metaFor(code) {
                    return difficultyMeta[code] || {
                        icon: 'fa-circle-question',
                        cls: '',
                        emoji: '⚪'
                    };
                }
                function slaLabel(code) {
                    const d = difficultyMap[code];
                    if (!d || d.expected_hours == null) return '';
                    const mins = Number(d.expected_hours);
                    if (mins < 60) {
                        return `SLA ${mins} menit`;
                    }
                    const hours = Math.round((mins / 60) * 10) / 10;
                    if (hours < 24) {
                        return `SLA ${mins} mnt (~${hours} jam)`;
                    }
                    const days = Math.round(hours / 24);
                    return `SLA ${mins} mnt (~${days} hari)`;
                }
                function loadDifficultyMaster(onDone) {
                    $.ajax({
                        url: DIFFICULTY_URL,
                        type: 'GET',
                        success: function(res) {
                            difficultyMap = {};
                            if (res.code == 200 && Array.isArray(res.data)) {
                                res.data.forEach(function(d) {
                                    difficultyMap[d.code] = d;
                                });
                            }
                            renderPriorityGrid();
                            if (typeof onDone === 'function') onDone();
                        },
                        error: function() {
                            difficultyMap = {};
                            $('#prio-grid').html(
                                '<div class="text-center text-muted py-3" style="font-size:12px; grid-column:span 2;">Gagal memuat data tingkat kesulitan.</div>'
                            );
                            if (typeof onDone === 'function') onDone();
                        }
                    });
                }
                function renderPriorityGrid() {
                    const codes = Object.keys(difficultyMap);
                    if (codes.length === 0) {
                        $('#prio-grid').html(
                            '<div class="text-center text-muted py-3" style="font-size:12px; grid-column:span 2;">Belum ada data tingkat kesulitan.</div>'
                        );
                        return;
                    }
                    let html = '';
                    codes.forEach(function(code) {
                        const d = difficultyMap[code];
                        const meta = metaFor(code);
                        html += `
                            <div class="priority-opt ${meta.cls}" data-val="${code}">
                                <div class="popt-dot"><i class="fa-solid ${meta.icon}"></i></div>
                                <div class="popt-body">
                                    <div class="popt-title">${escHtml(d.name || code)}</div>
                                    <div class="popt-sla">${escHtml(slaLabel(code))}</div>
                                </div>
                                <div class="popt-check">
                                    <div class="popt-check-inner"></div>
                                </div>
                            </div>
                        `;
                    });
                    $('#prio-grid').html(html);
                }
                /* ─── LOAD TICKETS ──────────────────── */
                function loadTickets() {
                    $('#ticket-list').html(skeletonHtml());
                    $.ajax({
                        url: GET_LIST_URL,
                        type: 'GET',
                        success: function(res) {
                            if (res.code == 200) {
                                allTickets = res.tickets ?? [];
                                if (res.summary) {
                                    $('#sum-total').text(res.summary.total ?? 0);
                                    $('#sum-unassigned').text(res.summary.unassigned ?? 0);
                                    $('#sum-inprogress').text(res.summary.inProgress ?? 0);
                                    $('#sum-closed').text(res.summary.closed ?? 0);
                                } else {
                                    updateSummary(allTickets);
                                }
                                renderList();
                            } else {
                                showEmpty(res.message || 'Tidak ada ticket tersedia.');
                            }
                        },
                        error: function(xhr) {
                            showEmpty(window.getFriendlyErrorMessage(xhr, 'Gagal memuat data ticket.'));
                        }
                    });
                }
                function updateSummary(tickets) {
                    const total = tickets.length;
                    const unassigned = tickets.filter(t => !t.pic_id).length;
                    const inProgress = tickets.filter(t => t.ticket_status === 'T04').length;
                    const closed = tickets.filter(t => t.ticket_status === 'T06').length;
                    $('#sum-total').text(total);
                    $('#sum-unassigned').text(unassigned);
                    $('#sum-inprogress').text(inProgress);
                    $('#sum-closed').text(closed);
                }
                /* ─── LOAD PIC LIST ─────────────────── */
                function loadPicList(callback) {
                    $.ajax({
                        url: GET_PIC_URL,
                        type: 'GET',
                        data: { ticket_id: currentTicket ? currentTicket.id : '' },
                        success: function(res) {
                            allPic = (res.code == 200) ? (res.pic ?? []) : [];
                            if (typeof callback === 'function') {
                                callback(allPic);
                            }
                        },
                        error: function(xhr) {
                            allPic = [];
                            console.warn('Gagal memuat daftar PIC', xhr);
                            if (typeof callback === 'function') {
                                callback(allPic);
                            }
                        }
                    });
                }

                /* ─── FORMAT HOURS TO READABLE ────────── */
                function formatHoursToReadable(hours) {
                    if (hours === null || hours === undefined || isNaN(hours)) return '0 mnt';
                    const num = Number(hours);
                    if (num <= 0) return '0 mnt';
                    if (num < 1) {
                        return Math.round(num * 60) + ' mnt';
                    }
                    return num.toFixed(1) + ' jam';
                }
                /* ─── LOAD KPI SUMMARY ────────────────── */
                function loadKpiSummary() {
                    $('#kpiContainer').html(`
                        <div class="kpi-skeleton-card">
                            <span class="skeleton" style="height:32px; width:32px; border-radius:50%; display:inline-block;"></span>
                            <div style="flex:1;">
                                <span class="skeleton" style="height:12px; width:50%; display:block; margin-bottom:4px;"></span>
                                <span class="skeleton" style="height:8px; width:30%; display:block;"></span>
                            </div>
                        </div>
                        <div class="kpi-skeleton-card">
                            <span class="skeleton" style="height:32px; width:32px; border-radius:50%; display:inline-block;"></span>
                            <div style="flex:1;">
                                <span class="skeleton" style="height:12px; width:50%; display:block; margin-bottom:4px;"></span>
                                <span class="skeleton" style="height:8px; width:30%; display:block;"></span>
                            </div>
                        </div>
                    `);
                    $.ajax({
                        url: KPI_URL,
                        type: 'GET',
                        success: function(res) {
                            if (res.code !== 200 || !res.data || res.data.length === 0) {
                                $('#kpi-btn-count').text('0 PIC');
                                $('#kpiContainer').html(`
                                    <div class="kpi-empty">
                                        <i class="fa-solid fa-users-slash me-1"></i> Belum ada data PIC yang tersedia.
                                    </div>
                                `);
                                return;
                            }
                            $('#kpi-btn-count').text(res.data.length + ' PIC');
                            renderKpiCards(res.data);
                        },
                        error: function(xhr) {
                            $('#kpi-btn-count').text('0 PIC');
                            $('#kpiContainer').html(`
                                <div class="kpi-empty text-danger">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Gagal memuat ringkasan KPI PIC.
                                </div>
                            `);
                        }
                    });
                }
                /* ─── RENDER KPI CARDS ────────────────── */
                function renderKpiCards(data) {
                    let html = '';
                    data.forEach(function(p) {
                        const initials = getInitials(p.pic_name);
                        const avatarHtml = p.pic_foto ? `
                            <img src="${p.pic_foto}" alt="${escHtml(p.pic_name)}" class="kpi-avatar-img" onerror="this.outerHTML='<div class=\\\'kpi-avatar-initial\\\'>${initials}</div>'">
                        ` : `
                            <div class="kpi-avatar-initial">${initials}</div>
                        `;
                        const ontimeRate = Math.min(100, Math.max(0, Number(p.ontime_rate ?? 100)));
                        let statusBadge = `<span class="kpi-status-badge good"><i class="fa-solid fa-circle-check"></i> Prima (${ontimeRate}%)</span>`;
                        let speedFillBg = 'linear-gradient(90deg, #10b981, #059669)';

                        if (ontimeRate < 70) {
                            statusBadge = `<span class="kpi-status-badge bad"><i class="fa-solid fa-triangle-exclamation"></i> Pantau (${ontimeRate}%)</span>`;
                            speedFillBg = 'linear-gradient(90deg, #ef4444, #dc2626)';
                        } else if (ontimeRate < 85) {
                            statusBadge = `<span class="kpi-status-badge warning"><i class="fa-solid fa-clock-rotate-left"></i> Standar (${ontimeRate}%)</span>`;
                            speedFillBg = 'linear-gradient(90deg, #f59e0b, #d97706)';
                        }
                        const totalAssigned = p.total_assigned ?? (Number(p.active_tickets || 0) + Number(p.closed_tickets || 0));
                        const activeCount = p.active_tickets ?? 0;
                        const closedCount = p.closed_tickets ?? (p.total_tickets ?? 0);
                        const totalDuration = formatHoursToReadable(p.total_actual_hours);
                        const avgDuration = formatHoursToReadable(p.avg_processing_hours);
                        html += `
                            <div class="kpi-card">
                                <div>
                                    <div class="kpi-card-header">
                                        <div class="karyawan-info">
                                            <div class="kpi-avatar-wrap">
                                                ${avatarHtml}
                                            </div>
                                            <div class="karyawan-meta">
                                                <div class="karyawan-name" title="${escHtml(p.pic_name)}">${escHtml(p.pic_name)}</div>
                                                <div class="karyawan-role" title="${escHtml(p.pic_jabatan || '-')}">${escHtml(p.pic_jabatan || '-')}</div>
                                            </div>
                                        </div>
                                        ${statusBadge}
                                    </div>

                                    <div class="kpi-metrics-compact">
                                        <div class="m-item">
                                            <span class="m-label">Assign</span>
                                            <span class="m-val text-dark">${totalAssigned}</span>
                                        </div>
                                        <div class="m-item">
                                            <span class="m-label">Aktif</span>
                                            <span class="m-val text-primary">${activeCount}</span>
                                        </div>
                                        <div class="m-item">
                                            <span class="m-label">Selesai</span>
                                            <span class="m-val text-success">${closedCount}</span>
                                        </div>
                                        <div class="m-item">
                                            <span class="m-label">Avg Waktu</span>
                                            <span class="m-val" style="color:#7c3aed;">${avgDuration}</span>
                                        </div>
                                    </div>

                                    <div class="kpi-speed-compact">
                                        <span class="kpi-speed-label"><i class="fa-solid fa-gauge-high text-primary me-1"></i>SLA: ${ontimeRate}%</span>
                                        <div class="kpi-speed-bar">
                                            <div class="kpi-speed-fill" style="width: ${ontimeRate}%; background: ${speedFillBg};"></div>
                                        </div>
                                    </div>
                                </div>

                                <a href="${KPI_DASHBOARD_URL}/${p.pic_id}" class="btn-kpi-detail" title="Lihat KPI ${escHtml(p.pic_name)}">
                                    <i class="fa-solid fa-chart-line"></i>
                                    <span>Detail KPI</span>
                                    <i class="fa-solid fa-chevron-right ms-auto" style="font-size:9px;"></i>
                                </a>
                            </div>
                        `;
                    });
                    $('#kpiContainer').html(html);
                }

                function getStatusBadge(t) {
                    const isAssigned = !!t.pic_id;
                    if (!isAssigned && t.ticket_status !== 'T08') {
                        return `<span class="tc-status-badge badge-unassigned"><i class="fa-solid fa-user-clock"></i> Belum Ditugaskan</span>`;
                    }

                    switch (t.ticket_status) {
                        case 'T04':
                            return `<span class="tc-status-badge badge-in-progress"><i class="fa-solid fa-spinner fa-spin"></i> Sedang Dikerjakan</span>`;
                        case 'T05':
                            return `<span class="tc-status-badge badge-waiting"><i class="fa-solid fa-hourglass-half"></i> Menunggu Respon Peminta</span>`;
                        case 'T07':
                            return `<span class="tc-status-badge badge-feedback"><i class="fa-solid fa-comment-dots"></i> Feedback Peminta</span>`;
                        case 'T06':
                            return `<span class="tc-status-badge badge-closed"><i class="fa-solid fa-circle-check"></i> Selesai</span>`;
                        case 'T08':
                            return `<span class="tc-status-badge badge-cancelled"><i class="fa-solid fa-ban"></i> Dibatalkan</span>`;
                        case 'T03':
                        default:
                            return isAssigned ? `<span class="tc-status-badge badge-assigned"><i class="fa-solid fa-user-check"></i> Ditugaskan</span>` : `<span class="tc-status-badge badge-unassigned"><i class="fa-solid fa-user-clock"></i> Belum Ditugaskan</span>`;
                    }
                }

                /* ── Does a single ticket match the active status tab? ── */
                function ticketMatchesFilter(t, filter) {
                    switch (filter) {
                        case 'unassigned': return !t.pic_id && t.ticket_status !== 'T08';
                        case 'assigned': return !!t.pic_id && (!t.ticket_status || t.ticket_status === 'T03');
                        case 'in_progress': return t.ticket_status === 'T04';
                        case 'feedback': return t.ticket_status === 'T07';
                        case 'closed': return t.ticket_status === 'T06';
                        case 'cancelled': return t.ticket_status === 'T08';
                        default: return true; // 'all'
                    }
                }

                /* ── Does a single ticket match the search box? ── */
                function ticketMatchesSearch(t, q) {
                    if (!q) return true;
                    return (t.nomor_ticket || '').toLowerCase().includes(q) ||
                        (t.requestor_name || '').toLowerCase().includes(q) ||
                        (t.menu_name || '').toLowerCase().includes(q) ||
                        (t.submenu_name || '').toLowerCase().includes(q) ||
                        (t.pic_name || '').toLowerCase().includes(q);
                }

                /* ─── RENDER LIST ───────────────────── */
                function renderList() {
                    const q = searchQuery.trim().toLowerCase();

                    // ── Group ALL tickets by root_ticket_id first, before filtering ──
                    const groups = {};
                    const allGroupOrder = [];
                    allTickets.forEach(function(t) {
                        const gKey = t.root_ticket_id || t.id;
                        if (!groups[gKey]) {
                            groups[gKey] = [];
                            allGroupOrder.push(gKey);
                        }
                        groups[gKey].push(t);
                    });

                    // ── Decide which groups pass the active tab + search ──
                    // Real group (2+ tiket / bagian dari induk-turunan): tampil kalau ADA salah satu
                    //   member yang cocok filter/search, lalu SEMUA member grup itu ikut ditampilkan
                    //   (jadi filter status berlaku untuk seluruh grup, bukan memotong sebagian tiket).
                    // Ticket individu (tidak tergabung grup): difilter per-tiket sendiri, jadi langsung
                    //   pindah tab begitu statusnya berubah, tanpa menunggu tiket lain.
                    const groupOrder = [];
                    allGroupOrder.forEach(function(gKey) {
                        const members = groups[gKey];
                        const isRealGroup = members.length > 1 || members[0].is_group;
                        const matches = isRealGroup
                            ? members.some(m => ticketMatchesFilter(m, activeFilter) && ticketMatchesSearch(m, q))
                            : (ticketMatchesFilter(members[0], activeFilter) && ticketMatchesSearch(members[0], q));
                        if (matches) groupOrder.push(gKey);
                    });

                    if (groupOrder.length === 0) {
                        let emptyMsg = 'Tidak ada ticket ditemukan.';
                        if (activeFilter === 'unassigned') emptyMsg = 'Semua ticket sudah di-assign ke PIC.';
                        else if (activeFilter === 'in_progress') emptyMsg = 'Tidak ada ticket yang sedang dikerjakan saat ini.';
                        else if (activeFilter === 'feedback') emptyMsg = 'Tidak ada ticket yang memerlukan revisi / feedback saat ini.';
                        else if (activeFilter === 'closed') emptyMsg = 'Belum ada ticket yang selesai.';
                        else if (activeFilter === 'cancelled') emptyMsg = 'Tidak ada ticket yang dibatalkan.';
                        showEmpty(emptyMsg);
                        $('#group-toggle-all').hide();
                        return;
                    }

                    // Sort tickets within each group: root first, then by created_at
                    groupOrder.forEach(function(gKey) {
                        groups[gKey].sort(function(a, b) {
                            if (a.is_root && !b.is_root) return -1;
                            if (!a.is_root && b.is_root) return 1;
                            return (a.tanggal_ticket || '').localeCompare(b.tanggal_ticket || '');
                        });
                    });

                    let hasGroups = false;
                    let html = '';

                    groupOrder.forEach(function(gKey) {
                        const members = groups[gKey];

                        // ── Standalone ticket (1 member, no group) → regular card ──
                        if (members.length === 1 && !members[0].is_group) {
                            html += buildStandaloneCard(members[0]);
                            return;
                        }

                        hasGroups = true;

                        // ── Group with 2+ members → accordion ──
                        const rootTicket = members.find(m => m.is_root) || members[0];
                        const totalCount = members.length;
                        const unassignedCount = members.filter(m => !m.pic_id && m.ticket_status !== 'T08').length;
                        const assignedCount = members.filter(m => !!m.pic_id).length;
                        const closedCount = members.filter(m => m.ticket_status === 'T06').length;
                        const cancelledCount = members.filter(m => m.ticket_status === 'T08').length;

                        const allAssigned = unassignedCount === 0 && assignedCount > 0;
                        const allClosed = closedCount === totalCount;
                        const shouldCollapse = allAssigned || allClosed;

                        let groupClass = shouldCollapse ? 'collapsed' : '';
                        if (allClosed) groupClass += ' all-closed';
                        else if (allAssigned) groupClass += ' all-assigned';

                        // Badges
                        let badges = `<span class="tg-badge tg-badge-count"><i class="fa-solid fa-layer-group"></i> ${totalCount} Tiket</span>`;
                        if (unassignedCount > 0) badges += `<span class="tg-badge tg-badge-unassigned"><i class="fa-solid fa-user-clock"></i> ${unassignedCount} Belum Assign</span>`;
                        if (assignedCount > 0) badges += `<span class="tg-badge tg-badge-assigned"><i class="fa-solid fa-user-check"></i> ${assignedCount} Assigned</span>`;
                        if (closedCount > 0) badges += `<span class="tg-badge tg-badge-closed"><i class="fa-solid fa-circle-check"></i> ${closedCount} Selesai</span>`;

                        // Requestor info
                        const requestorName = rootTicket.requestor_name || '-';
                        const menuLabel = rootTicket.menu_name ? escHtml(rootTicket.menu_name) : '';

                        // Sub-cards HTML
                        let subCardsHtml = '';
                        members.forEach(function(t) {
                            subCardsHtml += buildSubCard(t);
                        });

                        html += `
                            <div class="ticket-group ${groupClass}" data-group-id="${gKey}">
                                <div class="ticket-group-header">
                                    <div class="ticket-group-toggle"><i class="fa-solid fa-chevron-down"></i></div>
                                    <div class="ticket-group-info">
                                        <div class="ticket-group-title">
                                            <i class="fa-solid fa-sitemap me-1" style="font-size:11px;"></i>
                                            ${escHtml(rootTicket.nomor_ticket || '-')}
                                        </div>
                                        <div class="ticket-group-sub">
                                            <span><i class="fa-solid fa-user me-1" style="color:#a855f7;"></i>${escHtml(requestorName)}</span>
                                            ${menuLabel ? `<span style="color:#9ca3af;">•</span><span>${menuLabel}</span>` : ''}
                                        </div>
                                    </div>
                                    <div class="ticket-group-badges">
                                        ${badges}
                                    </div>
                                </div>
                                <div class="ticket-group-body">
                                    ${subCardsHtml}
                                </div>
                            </div>
                        `;
                    });

                    $('#ticket-list').html(html);
                    $('#group-toggle-all').toggle(hasGroups);
                }

                /* ── Build a standalone card (same as before, for ungrouped tickets) ── */
                function buildStandaloneCard(t) {
                    const initials = getInitials(t.requestor_name || '-');
                    const isAssigned = !!t.pic_id;
                    const isCancelled = t.ticket_status === 'T08';
                    const isClosed = t.ticket_status === 'T06';
                    const cardClass = isCancelled ? 'cancelled' : (isClosed ? 'closed' : (isAssigned ? 'assigned' : ''));
                    const picInitials = isAssigned ? getInitials(t.pic_name || '-') : '';
                    const dateLabel = t.tanggal_ticket ? formatDate(t.tanggal_ticket) : '-';
                    const daysDiff = t.tanggal_ticket ? daysBetween(t.tanggal_ticket) : null;
                    const dInfo = difficultyMap[t.priority];
                    const dMeta = metaFor(t.priority);
                    const prioBadge = dInfo ?
                        `<span class="prio-badge ${dMeta.cls}">${dMeta.emoji} ${escHtml(dInfo.name)}</span>` : '';
                    const statusBadge = getStatusBadge(t);

                    let kpiChips = '';
                    if (isAssigned) {
                        const kpiItems = [];
                        if (t.actual_hours != null) {
                            const isOver = !!t.overrun_flag;
                            const mins = Number(t.actual_hours);
                            const timeStr = mins < 60 ? `${mins} mnt` : `${mins} mnt (~${(mins/60).toFixed(1)} jam)`;
                            kpiItems.push(`<span class="tc-meta-item ${isOver ? 'text-danger' : 'text-success'}" style="font-weight:700;"><i class="fa-solid fa-stopwatch"></i> ${timeStr} ${isOver ? '(Over SLA)' : '(On Time)'}</span>`);
                        } else if (t.ticket_status === 'T04') {
                            kpiItems.push(`<span class="tc-meta-item text-primary" style="font-weight:600;"><i class="fa-solid fa-spinner fa-spin"></i> Proses Pengerjaan</span>`);
                        }
                        if (t.feedback_cycles > 0) {
                            kpiItems.push(`<span class="tc-meta-item text-danger" style="font-weight:600;"><i class="fa-solid fa-rotate-right"></i> ${t.feedback_cycles}x Revisi</span>`);
                        }
                        if (kpiItems.length > 0) {
                            kpiChips = `<div class="tc-meta mt-1">${kpiItems.join('')}</div>`;
                        }
                    }

                    let actionBtn = '';
                    if (isCancelled) {
                        actionBtn = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded d-inline-flex align-items-center justify-content-center" style="font-size:11px; font-weight:600; height:32px;"><i class="fa-solid fa-ban me-1"></i>Ticket Dibatalkan</span>`;
                    } else {
                        actionBtn = `<button class="btn-assign ${isAssigned ? 'btn-reassign' : ''} btn-open-modal" data-id="${t.id}"><i class="fa-solid fa-${isAssigned ? 'pen-to-square' : 'user-plus'}"></i> <span>${isAssigned ? 'Ubah Penugasan' : 'Assign PIC'}</span></button>`;
                    }

                    return `
                        <div class="ticket-card ${cardClass}"
                            data-id="${t.id}"
                            data-nomor="${escHtml(t.nomor_ticket)}"
                            data-menu="${escHtml(t.menu_name || '-')}"
                            data-submenu="${escHtml(t.submenu_name || '-')}"
                            data-priority="${t.priority || ''}"
                            data-requestor="${escHtml(t.requestor_name || '-')}"
                            data-pic-id="${t.pic_id || ''}"
                            data-pic-name="${escHtml(t.pic_name || '')}"
                            data-detail="${t.detail_count || 0}">

                            <div class="tc-top">
                                <div>
                                    <div class="tc-nomor">${escHtml(t.nomor_ticket || '-')}</div>
                                    <div class="mt-1 d-flex align-items-center flex-wrap gap-1">
                                        ${getEnvBadge(t.environment_kategori, t.environment_name, t.nomor_ticket)}
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:5px; flex-shrink:0;">
                                    ${prioBadge}
                                    ${statusBadge}
                                </div>
                            </div>

                            <div class="tc-meta">
                                ${t.menu_name    ? `<span class="tc-meta-item"><i class="fa-solid fa-folder-open"></i>${escHtml(t.menu_name)}</span>` : ''}
                                ${t.submenu_name ? `<span class="tc-meta-item"><i class="fa-solid fa-list-ul"></i>${escHtml(t.submenu_name)}</span>` : ''}
                                ${t.detail_count > 0 ? `<span class="tc-meta-item"><i class="fa-solid fa-table-list"></i>${t.detail_count} data</span>` : ''}
                                ${daysDiff !== null ? `
                                <span class="tc-meta-item" style="color:${daysDiff > 3 ? '#854f0b' : '#6b7280'};">
                                    <i class="fa-solid fa-hourglass-half" style="color:${daysDiff > 3 ? '#ef9f27' : '#1d9e75'};"></i>
                                    ${daysDiff === 0 ? 'Hari ini' : daysDiff + ' hari lalu'}
                                </span>` : ''}
                            </div>
                            ${kpiChips}

                            <div class="tc-footer">
                                <div class="tc-requester-group">
                                    <div class="tc-requester">
                                        <div class="tc-avatar">${initials}</div>
                                        <div style="min-width:0;">
                                            <div class="tc-name">${escHtml(t.requestor_name || '-')}</div>
                                            <div class="tc-date">${dateLabel}</div>
                                        </div>
                                    </div>
                                    ${isAssigned ? `
                                    <div class="tc-requester tc-pic-requester">
                                        <div class="tc-avatar pic-avatar">${picInitials}</div>
                                        <div style="min-width:0;">
                                            <div class="tc-name" style="color:#185fa5;">${escHtml(t.pic_name || '-')}</div>
                                            <div class="tc-date">PIC</div>
                                        </div>
                                    </div>` : ''}
                                </div>
                                <div class="tc-actions">
                                    <button class="btn-detail btn-open-detail" data-id="${t.id}" title="Lihat Detail &amp; KPI">
                                        <i class="fa-solid fa-eye"></i>
                                        <span class="btn-text-mobile">Lihat Detail Ticket</span>
                                    </button>
                                    <a href="{{ url('/ticket/export') }}/${t.id}" target="_blank" class="btn-detail btn-tc-pdf" title="Download / Print PDF" style="text-decoration:none;">
                                        <i class="fa-solid fa-file-pdf" style="color:#ef4444;"></i>
                                        <span class="btn-text-mobile">Export PDF</span>
                                    </a>
                                    ${actionBtn}
                                </div>
                            </div>
                        </div>
                    `;
                }

                /* ── Build a sub-card inside a group accordion ── */
                function buildSubCard(t) {
                    const initials = getInitials(t.requestor_name || '-');
                    const isAssigned = !!t.pic_id;
                    const isCancelled = t.ticket_status === 'T08';
                    const isClosed = t.ticket_status === 'T06';
                    const subClass = isCancelled ? 'sub-cancelled' : (isClosed ? 'sub-closed' : (isAssigned ? 'sub-assigned' : ''));
                    const picInitials = isAssigned ? getInitials(t.pic_name || '-') : '';
                    const dateLabel = t.tanggal_ticket ? formatDate(t.tanggal_ticket) : '-';
                    const statusBadge = getStatusBadge(t);

                    let typeBadge = '';
                    if (t.is_root) {
                        typeBadge = `<span class="badge" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe; font-size:9.5px; font-weight:700;"><i class="fa-solid fa-crown me-1"></i>Tiket Induk</span>`;
                    } else if (t.has_parent && t.parent_ticket_number) {
                        typeBadge = `<span class="badge" style="background:#e0f2fe; color:#0284c7; border:1px solid #bae6fd; font-size:9.5px; font-weight:700;"><i class="fa-solid fa-arrow-turn-down me-1"></i>Turunan</span>`;
                    }

                    let actionBtn = '';
                    if (isCancelled) {
                        actionBtn = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1 py-1 rounded" style="font-size:10px; font-weight:600;"><i class="fa-solid fa-ban me-1"></i>Dibatalkan</span>`;
                    } else {
                        actionBtn = `<button class="btn-assign ${isAssigned ? 'btn-reassign' : ''} btn-open-modal" data-id="${t.id}" style="font-size:10.5px; padding:5px 12px;"><i class="fa-solid fa-${isAssigned ? 'pen-to-square' : 'user-plus'}"></i> <span>${isAssigned ? 'Ubah' : 'Assign'}</span></button>`;
                    }

                    return `
                        <div class="ticket-sub-card ${subClass} ticket-card"
                            data-id="${t.id}"
                            data-nomor="${escHtml(t.nomor_ticket)}"
                            data-menu="${escHtml(t.menu_name || '-')}"
                            data-submenu="${escHtml(t.submenu_name || '-')}"
                            data-priority="${t.priority || ''}"
                            data-requestor="${escHtml(t.requestor_name || '-')}"
                            data-pic-id="${t.pic_id || ''}"
                            data-pic-name="${escHtml(t.pic_name || '')}"
                            data-detail="${t.detail_count || 0}">

                            <div class="tsc-top">
                                <div>
                                    <div class="tsc-nomor">${escHtml(t.nomor_ticket || '-')}</div>
                                    <div class="mt-1 d-flex align-items-center flex-wrap gap-1">
                                        ${getEnvBadge(t.environment_kategori, t.environment_name, t.nomor_ticket)}
                                        ${typeBadge}
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:4px; flex-shrink:0;">
                                    ${statusBadge}
                                </div>
                            </div>

                            <div class="tsc-meta">
                                ${t.menu_name    ? `<span class="tsc-meta-item"><i class="fa-solid fa-folder-open"></i>${escHtml(t.menu_name)}</span>` : ''}
                                ${t.submenu_name ? `<span class="tsc-meta-item"><i class="fa-solid fa-list-ul"></i>${escHtml(t.submenu_name)}</span>` : ''}
                                ${t.detail_count > 0 ? `<span class="tsc-meta-item"><i class="fa-solid fa-table-list"></i>${t.detail_count} data</span>` : ''}
                            </div>

                            <div class="tsc-footer">
                                <div class="tsc-requester-group">
                                    <div class="tc-requester">
                                        <div class="tc-avatar" style="width:24px; height:24px; font-size:10px;">${initials}</div>
                                        <div style="min-width:0;">
                                            <div class="tc-name" style="font-size:10.5px;">${escHtml(t.requestor_name || '-')}</div>
                                            <div class="tc-date">${dateLabel}</div>
                                        </div>
                                    </div>
                                    ${isAssigned ? `
                                    <div class="tc-requester tc-pic-requester" style="padding-left:8px;">
                                        <div class="tc-avatar pic-avatar" style="width:24px; height:24px; font-size:10px;">${picInitials}</div>
                                        <div style="min-width:0;">
                                            <div class="tc-name" style="color:#185fa5; font-size:10.5px;">${escHtml(t.pic_name || '-')}</div>
                                            <div class="tc-date">PIC</div>
                                        </div>
                                    </div>` : ''}
                                </div>
                                <div class="tsc-actions">
                                    <button class="btn-detail btn-open-detail" data-id="${t.id}" title="Lihat Detail" style="width:28px; height:28px; font-size:12px;">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    ${actionBtn}
                                </div>
                            </div>
                        </div>
                    `;
                }

                /* ─── GROUP ACCORDION TOGGLE ───────────── */
                $(document).on('click', '.ticket-group-header', function(e) {
                    e.stopPropagation();
                    $(this).closest('.ticket-group').toggleClass('collapsed');
                });

                /* ─── EXPAND ALL / COLLAPSE ALL ────────── */
                $(document).on('click', '#btn-expand-all', function() {
                    $('.ticket-group').removeClass('collapsed');
                });
                $(document).on('click', '#btn-collapse-all', function() {
                    $('.ticket-group').addClass('collapsed');
                });

                /* ─── Prevent sub-card clicks from toggling accordion ─── */
                $(document).on('click', '.ticket-group-body', function(e) {
                    e.stopPropagation();
                });

                /* ─── OPEN DETAIL MODAL ─────────────── */
                $(document).on('click', '.btn-open-detail', function(e) {
                    e.stopPropagation();
                    const card = $(this).closest('.ticket-card');
                    const ticketId = card.data('id');
                    // Tampilkan modal dengan loading
                    $('#modal-detail-body').html(loadingHtml());
                    $('#detail-nomor-sub').text(card.data('nomor') || '-');
                    $('#btn-detail-to-assign').hide();
                    $('#btn-detail-to-reassign').hide();
                    $('#btn-detail-export-pdf').hide();
                    $('#modalDetail').modal('show');
                    $.ajax({
                        url: GET_DETAIL_URL + ticketId,
                        type: 'GET',
                        success: function(res) {
                            if (res.code == 200) {
                                renderDetailBody(res.ticket);
                            } else {
                                $('#modal-detail-body').html(`<div class="p-4 text-center text-muted">${res.message}</div>`);
                            }
                        },
                        error: function() {
                            $('#modal-detail-body').html('<div class="p-4 text-center text-muted">Gagal memuat detail ticket.</div>');
                        }
                    });
                });
                function buildChainHtml(t) {
                    if (!t.chain_info) return '';
                    const ci = t.chain_info;
                    const summary = ci.summary || {};
                    const hasChain = !!ci.has_chain;
                    const items = ci.items || [];

                    let bannerHtml = '';
                    if (summary.type === 'warning') {
                        bannerHtml = `
                            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1.5px solid #fde68a !important;">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start gap-2">
                                        <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 3px 10px rgba(217, 119, 6, 0.25);">
                                            <i class="fa-solid fa-clock-rotate-left text-white" style="font-size: 16px;"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                                <span style="font-size: 10px; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    <i class="fa-solid fa-circle-info me-1"></i>Informasi Alur Proses
                                                </span>
                                                ${hasChain ? `<span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 10px; font-weight: 700; border-radius: 20px;"><i class="fa-solid fa-layer-group me-1"></i>${ci.done_tickets}/${ci.total_tickets} Tiket Selesai</span>` : ''}
                                            </div>
                                            <h6 class="mb-1 fw-bold" style="color: #92400e; font-size: 12.5px;">
                                                ${escHtml(summary.title || 'Pengajuan Sedang Menunggu Proses')}
                                            </h6>
                                            <p class="mb-2 text-dark" style="font-size: 11.5px; line-height: 1.4;">
                                                ${escHtml(summary.description || '-')}
                                            </p>
                                            ${summary.waiting_on ? `
                                            <div class="d-inline-flex align-items-center gap-2 px-2 py-1 rounded-3" style="background: #ffffff; border: 1px solid #fde68a; font-size: 11px; color: #78350f;">
                                                <i class="fa-solid fa-user-clock text-warning"></i>
                                                <span>Saat ini menunggu: <strong>${escHtml(summary.waiting_on)}</strong> ${summary.waiting_sub ? `<span class="text-muted">(${escHtml(summary.waiting_sub)})</span>` : ''}</span>
                                            </div>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else if (summary.type === 'success') {
                        bannerHtml = `
                            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1.5px solid #bbf7d0 !important;">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #16a34a, #15803d); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 3px 10px rgba(22, 163, 74, 0.25);">
                                            <i class="fa-solid fa-circle-check text-white" style="font-size: 18px;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold" style="color: #166534; font-size: 12.5px;">
                                                ${escHtml(summary.title || 'Seluruh Rangkaian Pengajuan Telah Selesai')}
                                            </h6>
                                            <p class="mb-0 text-muted" style="font-size: 11.5px;">
                                                ${escHtml(summary.description || 'Semua proses pada pengajuan ini telah selesai dikerjakan.')}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    let tableHtml = '';
                    if (hasChain && items.length > 0) {
                        tableHtml = `
                            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background: #ffffff; border: 1.5px solid #ede9fe !important; overflow: hidden;">
                                <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #f3e8ff; color: #7c3aed; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-sitemap" style="font-size: 12px;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 12px;">Rangkaian Pengajuan Terkait</h6>
                                            <small class="text-muted" style="font-size: 10px;">Seluruh tiket dalam satu rombongan pengajuan ini</small>
                                        </div>
                                    </div>
                                    <span class="badge" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; font-size: 10px; padding: 4px 8px; border-radius: 20px;">
                                        Total ${items.length} Tiket
                                    </span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0" style="font-size: 11px;">
                                            <thead style="background: #faf8ff; color: #4c1d95; font-size: 10px; text-transform: uppercase;">
                                                <tr>
                                                    <th class="ps-3 py-2" style="width: 30px;">No</th>
                                                    <th class="py-2">Nomor &amp; Jenis Tiket</th>
                                                    <th class="py-2">Modul / Layanan</th>
                                                    <th class="py-2">Status Saat Ini</th>
                                                    <th class="py-2">Petugas Ditunggu</th>
                                                    <th class="pe-3 py-2 text-center" style="width: 80px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${items.map((item, idx) => {
                                                    const isCurrent = !!item.is_current;
                                                    let statusBadgeHtml = '';
                                                    if (item.is_done && !item.is_rejected) {
                                                        statusBadgeHtml = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded" style="font-size: 9.5px; font-weight:700;"><i class="fa-solid fa-circle-check me-1"></i>Selesai</span>`;
                                                    } else if (item.is_rejected) {
                                                        statusBadgeHtml = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded" style="font-size: 9.5px; font-weight:700;"><i class="fa-solid fa-circle-xmark me-1"></i>Dibatalkan</span>`;
                                                    } else if (item.status_code === 'T00' || item.status_badge === 'status-draft') {
                                                        statusBadgeHtml = `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded" style="font-size: 9.5px; font-weight:700;"><i class="fa-solid fa-pencil me-1"></i>Draft</span>`;
                                                    } else {
                                                        statusBadgeHtml = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded" style="font-size: 9.5px; font-weight:700;"><i class="fa-solid fa-clock me-1"></i>${escHtml(item.status_label || '-')}</span>`;
                                                    }

                                                    let waitingHtml = '-';
                                                    if (item.waiting_on) {
                                                        waitingHtml = `
                                                            <div style="font-size: 11px; font-weight: 700; color: #374151;">
                                                                ${escHtml(item.waiting_on)}
                                                            </div>
                                                            <small class="text-muted" style="font-size: 9.5px;">
                                                                ${escHtml(item.waiting_role || '')} ${item.waiting_stage ? `(${escHtml(item.waiting_stage)})` : ''}
                                                            </small>
                                                        `;
                                                    } else if (item.is_done && !item.is_rejected) {
                                                        waitingHtml = `<span class="text-success" style="font-size: 10px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>`;
                                                    }

                                                    const viewUrl = item.link_url || `{{ url('/ticket/lihat') }}/${item.menu_id || t.menu_id || 1}/${item.id || item.ticket_id}`;
                                                    const actionHtml = isCurrent
                                                        ? `<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded" style="font-size: 9px; font-weight:700;">Tiket Ini</span>`
                                                        : `<a href="${viewUrl}" target="_blank" class="btn btn-sm btn-outline-primary px-2 py-1" style="font-size: 9.5px; font-weight:600; text-decoration:none;"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Lihat</a>`;

                                                    const envBadgeHtml = getEnvBadge(item.environment_kategori, item.environment_name, item.ticket_number);

                                                    return `
                                                        <tr style="${isCurrent ? 'background: #fdfaff;' : ''}">
                                                            <td class="ps-3 fw-bold text-muted">${idx + 1}</td>
                                                            <td>
                                                                <div class="fw-bold ${isCurrent ? 'text-primary' : 'text-dark'}" style="font-size: 11.5px;">
                                                                    ${escHtml(item.ticket_number || '-')}
                                                                </div>
                                                                <div class="d-flex align-items-center gap-1 mt-1">
                                                                    <span class="badge ${item.is_root ? 'bg-primary' : 'bg-info'}" style="font-size: 8.5px; font-weight: 700;">
                                                                        ${escHtml(item.type_label || (item.is_root ? 'Tiket Utama' : 'Tiket Turunan'))}
                                                                    </span>
                                                                    ${envBadgeHtml}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="fw-semibold text-dark">${escHtml(item.sub_menu_name || '-')}</div>
                                                                <small class="text-muted" style="font-size: 9.5px;">${escHtml(item.menu_name || '-')}</small>
                                                            </td>
                                                            <td>${statusBadgeHtml}</td>
                                                            <td>${waitingHtml}</td>
                                                            <td class="pe-3 text-center">${actionHtml}</td>
                                                        </tr>
                                                    `;
                                                }).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    return bannerHtml + tableHtml;
                }

                function buildParentCardHtml(t) {
                    const parentTicket = t.parent_ticket;
                    const rootTicket = t.root_ticket;
                    const childTickets = t.child_tickets || [];

                    const hasParent = !!parentTicket;
                    const hasRoot = !!rootTicket && (!parentTicket || rootTicket.id !== parentTicket.id) && (rootTicket.id !== t.id);
                    const hasChildren = childTickets.length > 0;

                    if (!hasParent && !hasRoot && !hasChildren) return '';

                    let parentSection = '';
                    if (hasParent) {
                        const pEnv = getEnvBadge(parentTicket.environment_kategori, null, parentTicket.nomor_ticket);
                        const pDate = parentTicket.created_at ? formatDate(parentTicket.created_at) : '-';
                        const pSub = [parentTicket.menu_name, parentTicket.submenu_name].filter(Boolean).join(' - ') || (parentTicket.submenu_name || '-');
                        const pLink = parentTicket.link_url || `{{ url('/ticket/lihat') }}/${parentTicket.ticket_menu_id || parentTicket.menu_id || t.menu_id || 1}/${parentTicket.id || parentTicket.ticket_id}`;
                        parentSection = `
                            <div style="background: white; border: 1px solid #bae6fd; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px;">
                                <div class="d-flex align-items-start gap-2">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0f2fe; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fa-solid fa-arrow-up-from-bracket" style="color: #0284c7; font-size: 12px;"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                            <small style="font-size: 9.5px; font-weight: 700; color: #0284c7; text-transform: uppercase; letter-spacing: 0.5px;">
                                                Pengajuan Asal / Pemicu (Parent)
                                            </small>
                                            ${pEnv}
                                        </div>
                                        <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 8px 10px;">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                                <div style="font-size: 12px; font-weight: 800; color: #0c4a6e;">
                                                    ${escHtml(parentTicket.nomor_ticket || '-')}
                                                </div>
                                                ${parentTicket.requestor_name ? `<small class="text-muted" style="font-size: 10px;"><i class="fa-solid fa-user me-1"></i>${escHtml(parentTicket.requestor_name)}</small>` : ''}
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                                <small style="color: #475569; font-size: 10.5px; font-weight: 600;">
                                                    <i class="fa-solid fa-layer-group me-1 text-primary"></i>${escHtml(pSub)}
                                                </small>
                                                <small style="color: #64748b; font-size: 9.5px;">
                                                    <i class="fa-solid fa-calendar me-1"></i>${pDate}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="mt-1 d-flex align-items-center gap-2">
                                            <a href="${pLink}" target="_blank" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 10px; color: #0284c7; font-weight: 600;">
                                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Lihat Detail Pengajuan Asal
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    let rootSection = '';
                    if (hasRoot) {
                        const rEnv = getEnvBadge(rootTicket.environment_kategori, null, rootTicket.nomor_ticket);
                        const rDate = rootTicket.created_at ? formatDate(rootTicket.created_at) : '-';
                        const rSub = [rootTicket.menu_name, rootTicket.submenu_name].filter(Boolean).join(' - ') || (rootTicket.submenu_name || '-');
                        const rLink = rootTicket.link_url || `{{ url('/ticket/lihat') }}/${rootTicket.ticket_menu_id || rootTicket.menu_id || t.menu_id || 1}/${rootTicket.id || rootTicket.ticket_id}`;
                        rootSection = `
                            <div style="background: white; border: 1px solid #ddd6fe; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px;">
                                <div class="d-flex align-items-start gap-2">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #f5f3ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fa-solid fa-sitemap" style="color: #7c3aed; font-size: 12px;"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                            <small style="font-size: 9.5px; font-weight: 700; color: #7c3aed; text-transform: uppercase; letter-spacing: 0.5px;">
                                                Pengajuan Pertama / Utama (Root)
                                            </small>
                                            ${rEnv}
                                        </div>
                                        <div style="background: #faf5ff; border: 1px solid #ddd6fe; border-radius: 8px; padding: 8px 10px;">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                                <div style="font-size: 12px; font-weight: 800; color: #581c87;">
                                                    ${escHtml(rootTicket.nomor_ticket || '-')}
                                                </div>
                                                ${rootTicket.requestor_name ? `<small class="text-muted" style="font-size: 10px;"><i class="fa-solid fa-user me-1"></i>${escHtml(rootTicket.requestor_name)}</small>` : ''}
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                                <small style="color: #475569; font-size: 10.5px; font-weight: 600;">
                                                    <i class="fa-solid fa-layer-group me-1 text-primary"></i>${escHtml(rSub)}
                                                </small>
                                                <small style="color: #64748b; font-size: 9.5px;">
                                                    <i class="fa-solid fa-calendar me-1"></i>${rDate}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="mt-1 d-flex align-items-center gap-2">
                                            <a href="${rLink}" target="_blank" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 10px; color: #7c3aed; font-weight: 600;">
                                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Lihat Detail Pengajuan Pertama
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    let childSection = '';
                    if (hasChildren) {
                        childSection = `
                            <div style="background: white; border: 1px solid #e9d5ff; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #f3e8ff; color: #7c3aed; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-network-wired" style="font-size: 11px;"></i>
                                        </div>
                                        <span style="font-size: 11px; font-weight: 700; color: #6b21a8; text-transform: uppercase;">
                                            Tiket Turunan yang Dihasilkan (${childTickets.length})
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    ${childTickets.map(c => {
                                        const cEnv = getEnvBadge(c.environment_kategori, null, c.nomor_ticket);
                                        const cDate = c.created_at ? formatDate(c.created_at) : '-';
                                        const cSub = [c.menu_name, c.submenu_name].filter(Boolean).join(' - ') || (c.submenu_name || '-');
                                        const cLink = c.link_url || `{{ url('/ticket/lihat') }}/${c.ticket_menu_id || c.menu_id || t.menu_id || 1}/${c.id || c.ticket_id}`;
                                        return `
                                            <div class="p-2 rounded-2" style="background: #faf5ff; border: 1px solid #ede9fe; font-size: 11px;">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                                    <div class="fw-bold text-dark">${escHtml(c.nomor_ticket || '-')}</div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        ${cEnv}
                                                        <a href="${cLink}" target="_blank" class="btn btn-sm btn-outline-primary px-1 py-0" style="font-size: 9px; text-decoration:none;">
                                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 text-muted" style="font-size: 10px;">
                                                    <span><i class="fa-solid fa-folder me-1 text-primary"></i>${escHtml(cSub)}</span>
                                                    <span>${c.pic_name ? `<i class="fa-solid fa-user-tie me-1"></i>${escHtml(c.pic_name)}` : '<span class="text-warning">Belum Ditugaskan</span>'}</span>
                                                </div>
                                            </div>
                                        `;
                                    }).join('')}
                                </div>
                            </div>
                        `;
                    }

                    return `
                        <div class="card mb-3" style="border-left: 4px solid #0284c7; border-radius: 12px; overflow: hidden; box-shadow: 0 3px 10px rgba(2, 132, 199, 0.08); border-top: 1px solid #e0f2fe; border-right: 1px solid #e0f2fe; border-bottom: 1px solid #e0f2fe;">
                            <div class="card-body p-3" style="background: linear-gradient(135deg, #f0f9ff, #ffffff);">
                                <div class="d-flex align-items-center mb-2">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #0284c7, #0ea5e9); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);">
                                        <i class="fa-solid fa-link text-white" style="font-size: 13px;"></i>
                                    </div>
                                    <div class="ms-2 flex-grow-1">
                                        <h6 class="mb-0 fw-bold" style="color: #0369a1; font-size: 12px;">
                                            <i class="fa-solid fa-history me-1"></i>Riwayat &amp; Asal Pengajuan
                                        </h6>
                                        <small class="text-muted" style="font-size: 10px;">Hubungan relasi tiket (induk / turunan / antar-environment)</small>
                                    </div>
                                </div>
                                ${parentSection}
                                ${rootSection}
                                ${childSection}
                            </div>
                        </div>
                    `;
                }

                function renderDetailBody(t) {
                    const isAssigned = !!t.pic_id;
                    const isCancelled = t.ticket_status === 'T08';
                    // Tombol footer
                    $('#btn-detail-export-pdf').attr('href', '{{ url("/ticket/export") }}/' + t.id).show();
                    if (isCancelled) {
                        $('#btn-detail-to-reassign').hide();
                        $('#btn-detail-to-assign').hide();
                    } else if (isAssigned) {
                        $('#btn-detail-to-reassign').show().data('ticket-id', t.id);
                        $('#btn-detail-to-assign').hide();
                    } else {
                        $('#btn-detail-to-assign').show().data('ticket-id', t.id);
                        $('#btn-detail-to-reassign').hide();
                    }
                    // chain ticket
                    const isChained = !!(t.root_ticket_id || t.parent_ticket_id);
                    const isRootSelf = !t.root_ticket_id || t.root_ticket_id === t.id;
                    const chainHtml = `
                        <div class="info-row">
                            <div class="info-row-label">Pengajuan Induk</div>
                            <div class="info-row-val">
                                ${isRootSelf
                                    ? `<span style="font-size:11px; color:#c4b5fd; font-style:italic;">—</span>`
                                    : `<span style="font-family:monospace; font-size:11px; background:#f5f3ff;
                                        border:1px solid #c4b5fd; border-radius:6px; padding:2px 8px; color:#6b21a8;">
                                        ${escHtml(t.root_nomor || t.root_ticket_id)}
                                    </span>`
                                }
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-row-label">Pengajuan Asal</div>
                            <div class="info-row-val">
                                ${t.parent_ticket_id
                                    ? `<span style="font-family:monospace; font-size:11px; background:#faf9ff;
                                        border:1px solid #ede9fe; border-radius:6px; padding:2px 8px; color:#6b21a8;">
                                        ${escHtml(t.parent_nomor || t.parent_ticket_id)}
                                    </span>`
                                    : `<span style="font-size:11px; color:#c4b5fd; font-style:italic;">—</span>`
                                }
                            </div>
                        </div>`;
                    // Status labels
                    const statusMetaMap = {
                        'T00': '<span class="status-pill pending"><i class="fa-solid fa-pen-to-square"></i> Draft</span>',
                        'T01': '<span class="status-pill pending"><i class="fa-solid fa-clock"></i> Menunggu Persetujuan</span>',
                        'T02': '<span class="status-pill approved"><i class="fa-solid fa-circle-check"></i> Disetujui</span>',
                        'T03': '<span class="status-pill ready"><i class="fa-solid fa-user-check"></i> Ditugaskan</span>',
                        'T04': '<span class="status-pill ready"><i class="fa-solid fa-spinner fa-spin"></i> Sedang Dikerjakan</span>',
                        'T05': '<span class="status-pill pending"><i class="fa-solid fa-hourglass-half"></i> Menunggu Respon Peminta</span>',
                        'T06': '<span class="status-pill approved"><i class="fa-solid fa-circle-check"></i> Selesai</span>',
                        'T07': '<span class="status-pill rejected"><i class="fa-solid fa-comment-dots"></i> Feedback Peminta</span>',
                        'T08': '<span class="status-pill rejected"><i class="fa-solid fa-ban"></i> Dibatalkan</span>',
                        'T13': '<span class="status-pill waiting_assign"><i class="fa-solid fa-clock"></i> Menunggu Ditugaskan</span>',
                        'T14': '<span class="status-pill pending"><i class="fa-solid fa-clock"></i> PENDING</span>',
                        'T15': '<span class="status-pill approved"><i class="fa-solid fa-circle-check"></i> Ditutup Otomatis oleh Sistem</span>',
                    };
                    const ticketStatusLabel = statusMetaMap[t.ticket_status] || `<span class="status-pill pending">${escHtml(t.ticket_status || '-')}</span>`;

                    const approvalLabel = {
                        'approved': '<span class="status-pill approved"><i class="fa-solid fa-circle-check"></i> Approved</span>',
                        'rejected': '<span class="status-pill rejected"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>',
                        'pending': '<span class="status-pill pending"><i class="fa-solid fa-clock"></i> Pending</span>',
                    } [t.approval_status] || `<span class="status-pill pending">${t.approval_status ?? '-'}</span>`;
                    const execLabel = {
                        'ready': '<span class="status-pill ready"><i class="fa-solid fa-circle-play"></i> Ready</span>',
                        'waiting': '<span class="status-pill pending"><i class="fa-solid fa-hourglass"></i> Waiting</span>',
                        'in_progress': '<span class="status-pill ready"><i class="fa-solid fa-spinner fa-spin"></i> In Progress</span>',
                        'done': '<span class="status-pill approved"><i class="fa-solid fa-check"></i> Done</span>',
                    } [t.execution_status] || `<span class="status-pill pending">${t.execution_status ?? '-'}</span>`;

                    // Signature boxes — dynamically filter required parties (hide approver 2 and checker if not needed)
                    const parties = [];

                    // 1. Pengaju (always shown)
                    parties.push({
                        label: 'Pengaju',
                        name: t.requestor_name,
                        at: t.created_at,
                        sig: t.requestor_signature
                    });

                    // 2. Approver 1 (always shown)
                    parties.push({
                        label: 'Approver 1',
                        name: t.approve1_name,
                        at: t.approve1_at,
                        sig: t.approve1_signature
                    });

                    // 3. Approver 2 (only if required / exists)
                    const hasApprove2 = !!(t.approve2_id || (t.approve2_name && t.approve2_name.trim() !== '' && t.approve2_name.trim() !== '-') || t.approve2_signature);
                    if (hasApprove2) {
                        parties.push({
                            label: 'Approver 2',
                            name: t.approve2_name,
                            at: t.approve2_at,
                            sig: t.approve2_signature
                        });
                    }

                    // 4. Checkers (only if required / exists)
                    if (t.checkers && t.checkers.length > 0) {
                        t.checkers.forEach(function(chk, idx) {
                            const chkLabel = t.checkers.length > 1 ? `Checker ${idx + 1}` : 'Checker';
                            parties.push({
                                label: chkLabel,
                                name: chk.karyawan_name,
                                at: chk.approved_at,
                                sig: chk.signature,
                                status: chk.status,
                                is_cross_environment: !!chk.is_cross_environment,
                                environment_name: chk.environment_name,
                                branch_sub_menu_name: chk.branch_sub_menu_name
                            });
                        });
                    } else {
                        const hasChecker = !!(t.checker_id || (t.checker_name && t.checker_name.trim() !== '' && t.checker_name.trim() !== '-') || t.checker_signature);
                        if (hasChecker) {
                            parties.push({
                                label: 'Checker',
                                name: t.checker_name,
                                at: t.checked_at,
                                sig: t.checker_signature,
                                is_cross_environment: false
                            });
                        }
                    }

                    // 5. Validator (always shown)
                    parties.push({
                        label: 'Validator',
                        name: t.validator_name,
                        at: t.validator_at,
                        sig: t.validator_signature
                    });

                    let sigHtml = '<div class="sig-grid">';
                    parties.forEach(function(p) {
                        const hasSig = !!p.sig;
                        const isDone = hasSig || p.status === 'completed' || p.status === 'approved';
                        const boxCls = isDone ? 'done' : 'pending-sig';
                        let imgHtml = '';
                        if (hasSig) {
                            imgHtml = `<img src="${escHtml(p.sig)}" alt="TTD ${escHtml(p.label)}">`;
                        } else if (p.status === 'completed' || p.status === 'approved') {
                            imgHtml = `<span class="badge bg-success" style="font-size:10px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>`;
                        } else if (p.is_cross_environment) {
                            imgHtml = `<span class="sig-empty" style="font-size:9px; color:#0f766e; line-height:1.2; padding:3px 2px;"><i class="fa-solid fa-clock me-1"></i>Belum diperiksa<br></span>`;
                        } else {
                            imgHtml = `<span class="sig-empty"><i class="fa-solid fa-clock me-1"></i>Menunggu</span>`;
                        }
                        const dateHtml = p.at ? `<div class="sig-box-date"><i class="fa-regular fa-clock me-1"></i>${formatDateTime(p.at)}</div>` : '';
                        const crossBadge = p.is_cross_environment ? `<div class="mt-1"><span class="badge" style="background:#ccfbf1; color:#0f766e; font-size:8px; border:1px solid #99f6e4; padding:2px 5px; white-space:normal; line-height:1.2;">Lintas Sistem</span></div>` : '';
                        sigHtml += `
                            <div class="sig-box ${boxCls}">
                                <div class="sig-box-label">
                                    <i class="fa-solid fa-${isDone ? 'circle-check' : 'clock'}" style="font-size:9px; margin-right:3px;"></i>
                                    ${escHtml(p.label)}
                                </div>
                                <div class="sig-img-wrap">${imgHtml}</div>
                                <div class="sig-box-name">${escHtml(p.name || '-')}</div>
                                ${crossBadge}
                                ${dateHtml}
                            </div>`;
                    });
                    sigHtml += '</div>';

                    let detailsHtml = '';
                    if (t.details && t.details.length > 0) {
                        t.details.forEach(function(d, idx) {
                            const collapseId = `assignDetail_${t.id || 'x'}_${idx}`; // PIC: `picDetail_${t.id || 'x'}_${idx}`
                            const hasSub = d.sub_details && d.sub_details.length > 0;
                            const subCount = hasSub ? d.sub_details.length : 1;
                            const cancelCount = hasSub ? d.sub_details.filter(s => s.is_cancelled).length : 0;
                            const tglTrx = formatDate(d.tanggal_transaksi);
                            let subHtml = '';
                            const refDetailSubHtml = d.reference_ticket_detail ? `
                                <div class="subdetail-field">
                                    <div class="subdetail-field-label">
                                        <i class="fa-solid fa-link text-primary"></i> Referensi Detail Sebelumnya
                                    </div>
                                    <div class="subdetail-field-val text-primary fw-semibold">
                                        ${escHtml(d.reference_ticket_detail.nomor_detail || '-')}${d.reference_ticket_detail.tanggal_transaksi ? ' (' + escHtml(formatDate(d.reference_ticket_detail.tanggal_transaksi)) + ')' : ''}
                                    </div>
                                </div>` : '';
                            if (hasSub) {
                                subHtml = `<div class="subdetails-wrap">` + d.sub_details.map((sd, sIdx) => `
                                    <div class="subdetail-card ${sd.is_cancelled ? 'is-line-cancelled' : ''}">
                                        ${lineCancelBox(sd)}
                                        <div class="subdetail-list">
                                            <div class="subdetail-field">
                                                <div class="subdetail-field-label">
                                                    <i class="fa-solid fa-receipt text-primary"></i> Nomor Transaksi
                                                </div>
                                                <div class="subdetail-field-val fw-bold text-dark">${escHtml(d.nomor_detail || '-')}</div>
                                            </div>
                                            <div class="subdetail-field">
                                                <div class="subdetail-field-label">
                                                    <i class="fa-solid fa-calendar-day text-info"></i> Tanggal Transaksi
                                                </div>
                                                <div class="subdetail-field-val">${escHtml(tglTrx)}</div>
                                            </div>
                                            ${refDetailSubHtml}
                                            <div class="subdetail-field">
                                                <div class="subdetail-field-label">
                                                    <i class="fa-solid fa-list-ol" style="color:#7c3aed;"></i> Baris / Line Detail
                                                </div>
                                                <div class="subdetail-field-val">${escHtml(sd.line_number || (sIdx + 1))}</div>
                                            </div>
                                            <div class="subdetail-field">
                                                <div class="subdetail-field-label">
                                                    <i class="fa-regular fa-comment-dots text-warning"></i> Alasan / Reason
                                                </div>
                                                <div class="subdetail-field-val">${escHtml(sd.alasan_ubah || '-')}</div>
                                            </div>
                                            <div class="subdetail-field">
                                                <div class="diff-box diff-before w-100">
                                                    <div class="diff-label"><i class="fa-solid fa-clock-rotate-left me-1"></i>Sebelum</div>
                                                    <div class="diff-content">${escHtml(sd.before_ubah || '-')}</div>
                                                </div>
                                            </div>
                                            <div class="subdetail-field">
                                                <div class="diff-box diff-after w-100">
                                                    <div class="diff-label"><i class="fa-solid fa-circle-check me-1"></i>Sesudah</div>
                                                    <div class="diff-content">${escHtml(sd.after_ubah || '-')}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `).join('') + `</div>`;
                            } else {
                                subHtml = `
                                    <div class="subdetail-card">
                                        <div class="subdetail-list">
                                            <div class="subdetail-field">
                                                <div class="subdetail-field-label">
                                                    <i class="fa-solid fa-receipt text-primary"></i> Nomor Transaksi
                                                </div>
                                                <div class="subdetail-field-val fw-bold text-dark">${escHtml(d.nomor_detail || '-')}</div>
                                            </div>
                                            <div class="subdetail-field">
                                                <div class="subdetail-field-label">
                                                    <i class="fa-solid fa-calendar-day text-info"></i> Tanggal Transaksi
                                                </div>
                                                <div class="subdetail-field-val">${escHtml(tglTrx)}</div>
                                            </div>
                                            ${refDetailSubHtml}
                                            <div class="subdetail-field">
                                                <div class="subdetail-field-label">
                                                    <i class="fa-solid fa-list-ol" style="color:#7c3aed;"></i> Baris / Line Detail
                                                </div>
                                                <div class="subdetail-field-val">
                                                    <span class="subdetail-line-badge">Baris #${escHtml(d.line_detail || '1')}</span>
                                                </div>
                                            </div>
                                            <div class="subdetail-field">
                                                <div class="subdetail-field-label">
                                                    <i class="fa-regular fa-comment-dots text-warning"></i> Alasan / Reason
                                                </div>
                                                <div class="subdetail-field-val">${escHtml(d.alasan_ubah || '-')}</div>
                                            </div>
                                            <div class="subdetail-field">
                                                <div class="diff-box diff-before w-100">
                                                    <div class="diff-label"><i class="fa-solid fa-clock-rotate-left me-1"></i>Sebelum</div>
                                                    <div class="diff-content">${escHtml(d.before_ubah || '-')}</div>
                                                </div>
                                            </div>
                                            <div class="subdetail-field">
                                                <div class="diff-box diff-after w-100">
                                                    <div class="diff-label"><i class="fa-solid fa-circle-check me-1"></i>Sesudah</div>
                                                    <div class="diff-content">${escHtml(d.after_ubah || '-')}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                            const refBadgePic = d.reference_ticket_detail ? `
                                <div class="text-truncate mt-1" style="font-size:10px; color:#6d28d9;">
                                    <i class="fa-solid fa-link me-1"></i>Ref: ${escHtml(d.reference_ticket_detail.nomor_detail || '-')}${d.reference_ticket_detail.tanggal_transaksi ? ' (' + escHtml(formatDate(d.reference_ticket_detail.tanggal_transaksi)) + ')' : ''}
                                </div>` : '';
                            detailsHtml += `
                                <div class="change-accordion-item">
                                    <div class="change-accordion-header"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#${collapseId}"
                                        aria-expanded="false"
                                        aria-controls="${collapseId}">
                                        <div class="change-card-num">${idx + 1}</div>
                                        <div class="change-card-info">
                                            <div class="nomor">Transaksi: ${escHtml(d.nomor_detail || '-')}</div>
                                            <div class="alasan">${subCount} baris perubahan${d.tanggal_transaksi ? ` · ${tglTrx}` : ''}${cancelCount ? ` · <span class="text-danger fw-bold">${cancelCount} dibatalkan</span>` : ''}</div>
                                            ${refBadgePic}
                                        </div>
                                        <div class="change-accordion-chevron">
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </div>
                                    </div>
                                    <div id="${collapseId}" class="collapse">
                                        <div class="change-accordion-body">
                                            ${subHtml}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        detailsHtml = `<div class="text-center text-muted" style="font-size:12px; padding:8px;">Tidak ada detail perubahan</div>`;
                    }
                    // Priority + SLA
                    const dInfo = difficultyMap[t.priority];
                    const dMeta = metaFor(t.priority);
                    const priorityHtml = (t.priority && dInfo) ? `
                        <div class="info-row" style="align-items:flex-start;">
                            <div class="info-row-label" style="padding-top:4px;">Prioritas</div>
                            <div class="info-row-val">
                                <span class="prio-detail-pill ${dMeta.cls}">
                                    <span class="prio-dot"></span>
                                    ${escHtml(dInfo.name)}
                                </span>
                                <div class="sla-strip ${dMeta.cls}">
                                    <i class="fa-solid fa-clock" style="font-size:10px;"></i>
                                    ${escHtml(slaLabel(t.priority))}
                                </div>
                            </div>
                        </div>` : (t.priority ? `
                        <div class="info-row">
                            <div class="info-row-label">Prioritas</div>
                            <div class="info-row-val">${escHtml(t.priority)}</div>
                        </div>` : '');

                    // KPI block — shown whenever ticket is assigned or has work sessions / actual hours
                    let kpiHtml = '';
                    if (isAssigned || t.actual_hours != null || t.pic_started_at || (t.work_sessions && t.work_sessions.length > 0)) {
                        const isOverrun = !!t.overrun_flag;
                        const isInProgress = (t.ticket_status === 'T04' || t.execution_status === 'in_progress');
                        const isFeedback = (t.ticket_status === 'T07');

                        let statusSlaHtml = '';
                        if (t.actual_hours != null) {
                            statusSlaHtml = `
                                <span class="${isOverrun ? 'kpi-overrun-badge' : 'kpi-ontime-badge'}">
                                    <i class="fa-solid ${isOverrun ? 'fa-triangle-exclamation' : 'fa-circle-check'}"></i>
                                    ${isOverrun ? 'Melebihi SLA' : 'Tepat Waktu'}
                                </span>`;
                        } else if (isInProgress) {
                            statusSlaHtml = `<span class="badge bg-info text-white" style="font-size:10px;"><i class="fa-solid fa-spinner fa-spin me-1"></i>Dalam Pengerjaan</span>`;
                        } else if (isFeedback) {
                            statusSlaHtml = `<span class="badge bg-danger text-white" style="font-size:10px;"><i class="fa-solid fa-comment-dots me-1"></i>Feedback Peminta</span>`;
                        } else {
                            statusSlaHtml = `<span class="badge bg-secondary text-white" style="font-size:10px;">Menunggu Proses</span>`;
                        }

                        let sessionsHtml = '';
                        if (t.work_sessions && t.work_sessions.length > 0) {
                            sessionsHtml = `
                                <div class="mt-3 pt-2 border-top">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold" style="font-size:11px; color:#4c1d95;">
                                            <i class="fa-solid fa-timeline me-1"></i>Riwayat Sesi Pengerjaan (${t.work_sessions.length})
                                        </span>
                                        ${(t.feedback_cycles && t.feedback_cycles > 0) ? `<span class="badge bg-danger text-white" style="font-size:9px;">${t.feedback_cycles}x Revisi</span>` : ''}
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        ${t.work_sessions.map(function(ws) {
                                            const isRevisi = ws.session_type === 'revisi';
                                            const isRunning = !ws.ended_at;
                                            return `
                                                <div class="p-2 rounded-3" style="background:#faf7ff; border:1px solid #ede9fe; font-size:10px;">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <span class="fw-bold ${isRevisi ? 'text-danger' : 'text-primary'}">
                                                            <i class="fa-solid ${isRevisi ? 'fa-rotate-right' : 'fa-play'} me-1"></i>
                                                            Siklus #${ws.cycle_number} (${isRevisi ? 'Revisi / Feedback' : 'Pengerjaan Awal'})
                                                        </span>
                                                        <span class="badge ${isRunning ? 'bg-primary text-white' : 'bg-light text-dark border'}">
                                                            ${isRunning ? '<i class="fa-solid fa-spinner fa-spin me-1"></i>Berjalan' : `${ws.duration_minutes || 0} mnt (${ws.duration_hours || 0} jam)`}
                                                        </span>
                                                    </div>
                                                    <div class="text-muted d-flex justify-content-between flex-wrap gap-1" style="font-size:9px;">
                                                        <span>Mulai: ${ws.started_at ? formatDateTime(ws.started_at) : '-'}</span>
                                                        <span>Selesai: ${ws.ended_at ? formatDateTime(ws.ended_at) : 'Sedang Berjalan'}</span>
                                                    </div>
                                                    ${ws.notes ? `<div class="mt-1 text-secondary fst-italic" style="font-size:9px;"><i class="fa-regular fa-comment me-1"></i>${escHtml(ws.notes)}</div>` : ''}
                                                </div>
                                            `;
                                        }).join('')}
                                    </div>
                                </div>`;
                        }

                        kpiHtml = `
                            <div class="kpi-box">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold" style="font-size:11px; color:#4c1d95;">
                                        <i class="fa-solid fa-chart-line me-1"></i>Indikator Kinerja (KPI)
                                    </span>
                                    ${(t.priority && difficultyMap[t.priority]) ? `
                                        <span class="badge" style="background:#ede9fe; color:#6b21a8; font-size:10px;">
                                            ${escHtml(difficultyMap[t.priority].name || t.priority)} (${slaLabel(t.priority)})
                                        </span>` : ''}
                                </div>
                                <div class="kpi-row">
                                    <span class="kpi-label">Mulai Dikerjakan</span>
                                    <span class="kpi-val">${t.pic_started_at ? formatDateTime(t.pic_started_at) : (isAssigned ? '<span class="text-muted fst-italic">Belum dimulai</span>' : '-')}</span>
                                </div>
                                <div class="kpi-row">
                                    <span class="kpi-label">Total Waktu Proses</span>
                                    <span class="kpi-val ${isOverrun ? 'overrun' : 'ontime'}">
                                        ${t.actual_hours != null ? (t.actual_hours < 60 ? `${t.actual_hours} menit` : `${t.actual_hours} menit (~${(t.actual_hours / 60).toFixed(1)} jam)`) : (isInProgress ? '<span class="text-info"><i class="fa-solid fa-spinner fa-spin me-1"></i>Sedang berjalan</span>' : '-')}
                                    </span>
                                </div>
                                ${t.expected_hours != null ? `
                                <div class="kpi-row">
                                    <span class="kpi-label">Target SLA</span>
                                    <span class="kpi-val">${t.expected_hours < 60 ? `${t.expected_hours} menit` : `${t.expected_hours} menit (~${(t.expected_hours / 60).toFixed(1)} jam)`}</span>
                                </div>
                                ` : ''}
                                ${t.variance_hours != null ? `
                                <div class="kpi-row">
                                    <span class="kpi-label">Varians Waktu</span>
                                    <span class="kpi-val ${t.variance_hours > 0 ? 'overrun' : 'ontime'}">${t.variance_hours > 0 ? '+' : ''}${t.variance_hours < 60 && t.variance_hours > -60 ? `${t.variance_hours} menit` : `${t.variance_hours} menit (~${(t.variance_hours / 60).toFixed(1)} jam)`}</span>
                                </div>
                                ` : ''}
                                <div class="kpi-row">
                                    <span class="kpi-label">Status SLA</span>
                                    ${statusSlaHtml}
                                </div>
                                ${sessionsHtml}
                            </div>`;
                    }

                    const picHtml = isAssigned ?
                        `<div class="info-row">
                <div class="info-row-label">PIC</div>
                    <div class="info-row-val">
                        <span class="pic-assigned-tag">
                            <i class="fa-solid fa-user-tie"></i> ${escHtml(t.pic_name)}
                        </span>
                    </div>
                </div>
            <div class="info-row">
            <div class="info-row-label">Assign oleh</div>
                <div class="info-row-val">${escHtml(t.assign_name || '-')}</div>
            </div>
            <div class="info-row">
                <div class="info-row-label">Tgl Assign</div>
                <div class="info-row-val">${t.assign_at ? formatDate(t.assign_at) : '-'}</div>
            </div>
            ${priorityHtml}
            ${kpiHtml}` :
                        `<div class="info-row">
                <div class="info-row-label">PIC</div>
                <div class="info-row-val">
                    <span style="color:#c4b5fd; font-style:italic;">
                        Belum di-assign
                    </span>
                </div>
            </div>
            ${priorityHtml}`;

                    const html = `
            ${buildChainHtml(t)}

            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fa-solid fa-info-circle"></i> Informasi Ticket
                </div>
            <div class="info-row">
                <div class="info-row-label">Nomor</div>
                <div class="info-row-val" style="color:#6b21a8; font-weight:800;">${escHtml(t.nomor_ticket)}</div>
            </div>
            <div class="info-row">
                <div class="info-row-label">Environment</div>
                <div class="info-row-val">${getEnvBadge(t.environment_kategori, t.environment_name, t.nomor_ticket)}</div>
            </div>
            <div class="info-row">
                <div class="info-row-label">Menu</div>
                <div class="info-row-val">${escHtml(t.menu_name || '-')}</div>
            </div>
            <div class="info-row">
                <div class="info-row-label">Sub Menu</div>
                <div class="info-row-val">${escHtml(t.submenu_name || '-')}</div>
            </div>
            <div class="info-row">
                <div class="info-row-label">Pengaju</div>
                <div class="info-row-val">${escHtml(t.requestor_name || '-')}</div>
            </div>
            <div class="info-row">
                <div class="info-row-label">Tanggal</div>
                <div class="info-row-val">${t.created_at ? formatDate(t.created_at) : '-'}</div>
            </div>
            <div class="info-row">
                <div class="info-row-label">Status</div>
                <div class="info-row-val">${ticketStatusLabel} ${approvalLabel} ${execLabel}</div>
            </div>
            <div class="info-row" style="align-items:flex-start;">
                <div class="info-row-label" style="padding-top:6px;">Lampiran</div>
                <div class="info-row-val">
                    ${(() => {
                        const list = (t.attachments && t.attachments.length > 0)
                            ? t.attachments
                            : (t.lampiran ? [{
                                file_path: t.lampiran,
                                original_name: t.lampiran.split('/').pop(),
                                file_size: null
                            }] : []);

                        if (list.length === 0) {
                            return '<span class="lampiran-none"><i class="fa-solid fa-paperclip me-1"></i>Tidak ada lampiran</span>';
                        }

                        const iconMap = {
                            pdf: 'fa-file-pdf',
                            doc: 'fa-file-word',
                            docx: 'fa-file-word',
                            xls: 'fa-file-excel',
                            xlsx: 'fa-file-excel',
                            csv: 'fa-file-excel',
                            zip: 'fa-file-zipper',
                            rar: 'fa-file-zipper',
                            jpg: 'fa-file-image',
                            jpeg: 'fa-file-image',
                            png: 'fa-file-image',
                            webp: 'fa-file-image',
                        };

                        return `<div class="d-flex flex-wrap gap-2">` + list.map(att => {
                            const url = att.file_path || '';
                            const filename = att.original_name || (url.split('/').pop()) || 'Lampiran';
                            const ext = filename.split('.').pop().toLowerCase();
                            const icon = iconMap[ext] || 'fa-file';
                            const sizeLabel = att.file_size ? `${(att.file_size / 1024).toFixed(1)} KB` : '';
                            return `<a href="${url}" target="_blank" class="lampiran-box">
                                <div class="lampiran-icon"><i class="fa-solid ${icon}"></i></div>
                                <div class="lampiran-info">
                                    <div class="lampiran-name" title="${escHtml(filename)}">${escHtml(filename)}</div>
                                    <div class="lampiran-meta">
                                        ${sizeLabel ? `<span class="me-1">${sizeLabel}</span> • ` : ''}
                                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:9px;"></i> Buka
                                    </div>
                                </div>
                            </a>`;
                        }).join('') + `</div>`;
                    })()}
                </div>
            </div>
            ${picHtml}
            ${chainHtml}
        </div>

        <div class="detail-section">
            <div class="detail-section-title">
                <i class="fa-solid fa-table-list"></i> Detail Perubahan (${t.details ? t.details.length : 0} data)
            </div>
            ${detailsHtml}
        </div>

        <div class="detail-section">
            <div class="detail-section-title">
                <i class="fa-solid fa-pen-nib"></i> Otorisasi &amp; Tanda Tangan
            </div>
            ${sigHtml}
        </div>

        ${buildParentCardHtml(t)}

        ${(() => {
            if (!t.comments || t.comments.length === 0) return '';

            const levelMap = {
                'approve1':  { label: 'Approver 1',  icon: 'fa-user-shield',   color: '#2563eb', bg: '#eff6ff',  border: '#bfdbfe' },
                'approve2':  { label: 'Approver 2',  icon: 'fa-user-shield',   color: '#7c3aed', bg: '#f5f3ff',  border: '#c4b5fd' },
                'checker':   { label: 'Checker',     icon: 'fa-list-check',    color: '#0891b2', bg: '#ecfeff',  border: '#a5f3fc' },
                'validator': { label: 'Validator',    icon: 'fa-clipboard-check', color: '#059669', bg: '#ecfdf5', border: '#a7f3d0' },
                'requestor': { label: 'Pemohon',      icon: 'fa-user',          color: '#d97706', bg: '#fffbeb',  border: '#fde68a' },
                'pic':       { label: 'PIC',          icon: 'fa-user-tie',      color: '#0284c7', bg: '#f0f9ff',  border: '#bae6fd' },
            };
            const actionMap = {
                'approved':  { label: 'Disetujui',  icon: 'fa-circle-check',         color: '#16a34a' },
                'rejected':  { label: 'Ditolak',    icon: 'fa-circle-xmark',         color: '#dc2626' },
                'pending':   { label: 'Pending',    icon: 'fa-clock',                color: '#d97706' },
                'validated': { label: 'Divalidasi', icon: 'fa-clipboard-check',      color: '#059669' },
                'checked':   { label: 'Dicek',      icon: 'fa-list-check',           color: '#0891b2' },
                'feedback':  { label: 'Feedback',   icon: 'fa-comment-dots',         color: '#7c3aed' },
                'close':     { label: 'Ditutup',    icon: 'fa-circle-check',         color: '#16a34a' },
            };

            const commentsHtml = t.comments.map(c => {
                const lv = levelMap[c.level] || { label: c.level || '-', icon: 'fa-comment', color: '#6b7280', bg: '#f9fafb', border: '#e5e7eb' };
                const ac = actionMap[c.action] || { label: c.action || '-', icon: 'fa-circle', color: '#6b7280' };
                const dateStr = c.date ? formatDateTime(c.date) : '-';

                return '<div style="background:' + lv.bg + '; border:1.5px solid ' + lv.border + '; border-radius:14px; padding:12px 14px; margin-bottom:8px;">'
                    + '<div class="d-flex align-items-center justify-content-between mb-2">'
                    +   '<div class="d-flex align-items-center gap-2">'
                    +     '<span style="width:28px; height:28px; border-radius:50%; background:' + lv.color + '; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:11px; flex-shrink:0;">'
                    +       '<i class="fa-solid ' + lv.icon + '"></i>'
                    +     '</span>'
                    +     '<div>'
                    +       '<div style="font-size:11px; font-weight:700; color:' + lv.color + ';">' + escHtml(lv.label) + '</div>'
                    +       '<div style="font-size:10px; color:#6b7280;">' + escHtml(c.name || '-') + '</div>'
                    +     '</div>'
                    +   '</div>'
                    +   '<div class="text-end">'
                    +     '<span style="font-size:10px; font-weight:700; color:' + ac.color + ';">'
                    +       '<i class="fa-solid ' + ac.icon + ' me-1"></i>' + escHtml(ac.label)
                    +     '</span>'
                    +     '<div style="font-size:9px; color:#9ca3af;">' + dateStr + '</div>'
                    +   '</div>'
                    + '</div>'
                    + '<div style="background:#fff; border:1px solid ' + lv.border + '; border-radius:8px; padding:8px 12px; font-size:12px; color:#374151; line-height:1.6;">'
                    +   escHtml(c.text || '-')
                    + '</div>'
                    + '</div>';
            }).join('');

            return '<div class="detail-section">'
                + '<div class="detail-section-title">'
                +   '<i class="fa-solid fa-comments"></i> Komentar (' + t.comments.length + ')'
                + '</div>'
                + commentsHtml
                + '</div>';
        })()}`;

    $('#modal-detail-body').html(html);
    }

    /* ─── DETAIL → ASSIGN BRIDGE ────────── */
    $(document).on('click', '#btn-detail-to-assign, #btn-detail-to-reassign', function() {
        const ticketId = $(this).data('ticket-id');
        const card = $(`.ticket-card[data-id="${ticketId}"]`);
        $('#modalDetail').modal('hide');
        setTimeout(function() {
            card.find('.btn-open-modal').trigger('click');
        }, 350);
    });

    /* ─── OPEN ASSIGN MODAL ─────────────── */
    $(document).on('click', '.btn-open-modal', function(e) {
        e.stopPropagation();
        const card = $(this).closest('.ticket-card');
        currentTicket = {
            id: card.data('id'),
            nomor: card.data('nomor'),
            menu: card.data('menu'),
            submenu: card.data('submenu'),
            requestor: card.data('requestor'),
            picId: card.data('pic-id') || null,
            picName: card.data('pic-name') || null,
            detail: card.data('detail')
        };
        selectedPicId = null;
        $('#assign-note').val('');
        $('#btn-confirm-assign').prop('disabled', true);
        $('#pic-search').val('');
        selectedPriority = null;
        $('.priority-opt').removeClass('selected');
        $('#sla-info-pill').hide().attr('class', 'sla-info-pill');
        $('#modal-ticket-nomor').text(currentTicket.nomor);
        $('#modal-ti-nomor').text(currentTicket.nomor);
        $('#modal-ti-detail').text(
            [currentTicket.menu, currentTicket.submenu].filter(Boolean).join(' › ') +
            (currentTicket.detail > 0 ? ` — ${currentTicket.detail} data` : '')
        );
        if (currentTicket.picId) {
            $('#modal-current-pic-tag').html(`
            <span class="pic-assigned-tag">
                <i class="fa-solid fa-user-tie"></i>
                ${escHtml(currentTicket.picName)}
            </span>`);
            $('#modal-current-pic').show();
        } else {
            $('#modal-current-pic').hide();
        }
        allPic = [];  
        renderPicGrid('');
        loadPicList(function() {
            renderPicGrid($('#pic-search').val());
        });
        $('#modalAssign').modal('show');
    });

    /* ─── RENDER PIC GRID ───────────────── */
    function renderPicGrid(query) {
        if (allPic.length === 0) {
            $('#pic-grid').html(`
            <div class="text-center text-muted py-4" style="font-size:12px; grid-column:span 2;">
                <i class="fa-solid fa-user-slash mb-2" style="font-size:24px; color:#c4b5fd;"></i>
                <br>Belum ada PIC terdaftar.
            </div>`);
            return;
        }
        let pics = allPic;
        if (query.trim() !== '') {
            const q = query.toLowerCase();
            pics = allPic.filter(p =>
                (p.name || '').toLowerCase().includes(q) ||
                (p.role || '').toLowerCase().includes(q)
            );
        }
        if (pics.length === 0) {
            $('#pic-grid').html(`<div class="text-center text-muted py-3" style="font-size:12px; grid-column:span 2;">PIC tidak ditemukan</div>`);
            return;
        }
        let html = '';
        pics.forEach(function(p) {
            const initials = getInitials(p.name || '-');
            const isSelected = String(p.id) === String(selectedPicId);
            const load = p.active_tickets ?? 0;
            const loadClass = load <= 15 ? 'load-low' : load <= 25 ? 'load-mid' : 'load-high';
            const loadLabel = load <= 15 ? 'Ringan' : load <= 25 ? 'Sedang' : 'Padat';
            html += `
            <div class="pic-card ${isSelected ? 'selected' : ''}"
                data-pic-id="${p.id}"
                data-pic-name="${escHtml(p.name)}">
                <div class="pic-check"><i class="fa-solid fa-check" style="font-size:9px;"></i></div>
                <div class="pic-av">${initials}</div>
                <div class="pic-name" title="${escHtml(p.name)}">${escHtml(p.name)}</div>
                <div class="pic-role" title="${escHtml(p.role || '-')}">${escHtml(p.role || '-')}</div>
                <span class="pic-load ${loadClass}">
                    <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                    ${load} Ticket Aktif &middot; ${loadLabel}
                </span>
            </div>`;
        });
        $('#pic-grid').html(html);
    }

    /* ─── SELECT PIC ────────────────────── */
    $(document).on('click', '.pic-card', function() {
        selectedPicId = $(this).data('pic-id');
        $('.pic-card').removeClass('selected');
        $(this).addClass('selected');
        checkAssignButton();
    });

    $('#pic-search').on('input', function() {
        renderPicGrid($(this).val());
    });

    /* ─── CONFIRM ASSIGN ────────────────── */
    $('#btn-confirm-assign').on('click', function() {
        if (!selectedPicId || !currentTicket) return;
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Memproses...');
        $.ajax({
            url: ASSIGN_URL,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ticket_id: currentTicket.id,
                pic_id: selectedPicId,
                priority: selectedPriority,
                note: $('#assign-note').val().trim()
            },
            success: function(res) {
                $('#modalAssign').modal('hide');
                if (res.code == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'PIC berhasil di-assign.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadTickets();
                    loadPicList();
                    loadKpiSummary();
                } else if (res.code == 422) {
                    let errMsg = res.message;
                    if (res.errors) {
                        errMsg = Object.values(res.errors).flat().join('<br>');
                    }
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        html: errMsg || 'Data penugasan belum lengkap.'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: window.getFriendlyErrorMessage(res.message, 'Gagal menyimpan penugasan ticket.')
                    });
                }
            },
            error: function(xhr) {
                $('#modalAssign').modal('hide');
                Swal.fire({
                    icon: (xhr.status === 422 || xhr.responseJSON?.code === 422) ? 'warning' : 'error',
                    title: (xhr.status === 422 || xhr.responseJSON?.code === 422) ? 'Validasi Gagal' : 'Gagal',
                    html: window.getFriendlyErrorMessage(xhr, 'Tidak dapat terhubung ke server.')
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i> Assign Sekarang');
            }
        });
    });

    /* ─── FILTER & SEARCH ───────────────── */
    $(document).on('click', '.filter-tab', function() {
        $('.filter-tab').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        renderList();
    });
    let searchTimer;
    $('#search-input').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            searchQuery = $(this).val();
            renderList();
        }, 300);
    });

    /* ─── REFRESH ───────────────────────── */
    $('#btn-refresh').on('click', function() {
        const icon = $(this).find('i');
        icon.addClass('fa-spin');
        loadTickets();
        loadPicList();
        loadKpiSummary();
        setTimeout(() => icon.removeClass('fa-spin'), 1000);
    });

    /* ─── PRIORITY GRID (dynamic — built by renderPriorityGrid() from difficultyMap) ── */
    let selectedPriority = null;

    $(document).on('click', '.priority-opt', function() {
        $('.priority-opt').removeClass('selected');
        $(this).addClass('selected');
        selectedPriority = $(this).data('val');

        const meta = metaFor(selectedPriority);
        $('#sla-info-pill')
            .attr('class', 'sla-info-pill ' + meta.cls)
            .html(`<i class="fa-solid fa-clock"></i> ${escHtml(slaLabel(selectedPriority))} · SLA ditentukan otomatis`)
            .show();

        checkAssignButton();
    });

    /* ─── HELPERS ───────────────────────── */
    function showEmpty(msg) {
        $('#ticket-list').html(`
        <div class="empty-state">
            <i class="fa-solid fa-inbox"></i>
            <p>${msg}</p>
            <small>Coba ubah filter atau refresh halaman</small>
        </div>`);
    }

    function loadingHtml() {
        return `<div class="detail-skeleton">
            <div class="d-flex flex-column gap-3 p-3">
                <span class="skeleton" style="height:14px; width:80%; display:block;"></span>
                <span class="skeleton" style="height:14px; width:55%; display:block;"></span>
                <span class="skeleton" style="height:100px; width:100%; display:block; border-radius:12px;"></span>
                <span class="skeleton" style="height:160px; width:100%; display:block; border-radius:12px;"></span>
                <span class="skeleton" style="height:80px;  width:100%; display:block; border-radius:12px;"></span>
            </div>
        </div>`;
    }

    function skeletonHtml() {
        let h = '';
        for (let i = 0; i < 4; i++) {
            h += `<div class="skeleton-card">
            <div class="d-flex justify-content-between mb-2">
                <span class="skeleton" style="height:14px; width:60%; display:inline-block;"></span>
                <span class="skeleton" style="height:14px; width:20%; display:inline-block;"></span>
            </div>
            <div class="d-flex gap-2 mb-2">
                <span class="skeleton" style="height:22px; width:90px; display:inline-block; border-radius:8px;"></span>
                <span class="skeleton" style="height:22px; width:70px; display:inline-block; border-radius:8px;"></span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="skeleton" style="height:28px;width:28px;border-radius:50%;display:inline-block;"></span>
                    <span class="skeleton" style="height:12px; width:80px; display:inline-block;"></span>
                </div>
                <span class="skeleton" style="height:28px; width:90px; display:inline-block; border-radius:50px;"></span>
            </div>
        </div>`;
        }
        return h;
    }

    function getInitials(name) {
        return (name || '-').split(' ').slice(0, 2).map(w => w[0] || '').join('').toUpperCase();
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr);
        if (isNaN(d)) return dateStr;
        return d.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function formatDateTime(dateStr) {
        const d = new Date(dateStr);
        if (isNaN(d)) return dateStr;
        return d.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function daysBetween(dateStr) {
        const d = new Date(dateStr);
        if (isNaN(d)) return null;
        return Math.floor((Date.now() - d.getTime()) / 86400000);
    }

    function escHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function lineCancelBox(sd) {
        if (!sd || !sd.is_cancelled) return '';
        const role = sd.cancelled_by_role === 'checker' ? 'oleh Checker'
            : (sd.cancelled_by_role === 'validator' ? 'oleh Validator' : 'otomatis (mengikuti transaksi induk)');
        return `<div class="line-cancel-box">
            <div class="lcb-title"><i class="fa-solid fa-ban me-1"></i>Baris dibatalkan ${role}${sd.cancelled_by_name ? ' (' + escHtml(sd.cancelled_by_name) + ')' : ''}</div>
            <div class="lcb-reason">${escHtml(sd.cancel_reason || '-')}</div>
        </div>`;
    }

    window.onbeforeunload = function() {
        Swal.fire({
            allowOutsideClick: false,
            background: 'transparent',
            html: '<div class="spinner-grow text-success spinner-grow-sm me-2"></div>'.repeat(3),
            showCancelButton: false,
            showConfirmButton: false,
        });
    };

    function checkAssignButton() {
        $('#btn-confirm-assign').prop('disabled', !(selectedPicId && selectedPriority));
    }

    // Difficulty master must be loaded before we render tickets (badges) and the
    // priority grid — everything else can load in parallel.
    loadDifficultyMaster(function() {
        loadTickets();
        loadPicList();
        loadKpiSummary();
    });

    // Auto-refresh data PIC & KPI di background setiap 30 detik agar selalu up-to-date
    setInterval(function() {
        if (!$('#modalAssign').hasClass('show')) {
            loadPicList();
            loadKpiSummary();
        }
    }, 30000);
    });
</script>
@endsection