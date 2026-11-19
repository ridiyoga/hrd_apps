{{-- =====================================================================
     admin.ticket.partials.canvas_viewport
     Interactive Workflow Canvas & Dependency editor.
     Included inside a `position:relative; height:720px; overflow:hidden`
     tab-pane. All positioning below is absolute *relative to that
     container*, not the viewport, so it composes safely with the
     surrounding dashboard chrome.
===================================================================== --}}
<style>
    #canvas-root {
        --cv-primary: #7c3aed;
        --cv-primary-dark: #5b21b6;
        --cv-primary-soft: #efe9fe;
        --cv-approver: #9055fd;
        --cv-approver-dark: #7439e0;
        --cv-checker: #03c3ec;
        --cv-checker-dark: #0aa3c7;
        --cv-validator: #28c76f;
        --cv-validator-dark: #1fa85c;
        --cv-final: #ff4d49;
        --cv-final-dark: #e13d3a;
        --cv-neutral: #8a92a6;
        --cv-neutral-dark: #6b7385;
        --cv-bg: #f6f5fc;
        --cv-dot: #e1ddf4;
        --cv-card: #ffffff;
        --cv-text: #33293f;
        --cv-text-soft: #766f88;
        --cv-radius: 11px;
        --cv-shadow: 0 6px 18px rgba(58, 32, 110, .12);
        --cv-shadow-lg: 0 18px 46px rgba(40, 20, 80, .22);
        --cv-sidebar-w: 230px;
        --cv-toolbar-h: 52px;
        position: absolute;
        inset: 0;
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--cv-text);
        border-radius: .5rem;
        border: 1px solid #e2d9f3;
    }

    #canvas-root * {
        box-sizing: border-box;
    }

    /* Toolbar */
    #canvas-root .cv-toolbar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: var(--cv-toolbar-h);
        background: linear-gradient(135deg, var(--cv-primary), var(--cv-primary-dark));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 14px;
        z-index: 60;
        box-shadow: 0 2px 12px rgba(80, 40, 160, .25);
    }

    #canvas-root .cv-toolbar .cv-brand {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .2px;
    }

    #canvas-root .cv-toolbar .cv-hint {
        font-size: 10.5px;
        opacity: .9;
        display: flex;
        gap: 10px;
    }

    #canvas-root .cv-toolbar .cv-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #canvas-root .cv-toolbar button {
        background: rgba(255, 255, 255, .16);
        border: 1px solid rgba(255, 255, 255, .28);
        color: #fff;
        height: 28px;
        border-radius: 7px;
        cursor: pointer;
        font-size: 12.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
        padding: 0 9px;
    }

    #canvas-root .cv-toolbar button:hover {
        background: rgba(255, 255, 255, .3);
    }

    #canvas-root .cv-toolbar button.cv-icon {
        width: 28px;
        padding: 0;
    }

    #canvas-root .cv-toolbar .cv-zoomlabel {
        font-size: 11px;
        min-width: 36px;
        text-align: center;
        opacity: .9;
    }

    /* Sidebar */
    #canvas-root .cv-sidebar {
        position: absolute;
        top: var(--cv-toolbar-h);
        left: 0;
        bottom: 0;
        width: var(--cv-sidebar-w);
        background: var(--cv-card);
        border-right: 1px solid #ece8fb;
        z-index: 45;
        display: flex;
        flex-direction: column;
    }

    #canvas-root .cv-sidebar .sb-head {
        padding: 10px 10px 8px;
        background: #faf9fe;
        border-bottom: 1px solid #f0ecfb;
    }

    #canvas-root .cv-sidebar .sb-head h4 {
        margin: 0;
        font-size: 11px;
        color: var(--cv-text-soft);
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    #canvas-root .cv-sidebar .sb-search {
        width: 100%;
        border: 1.5px solid #e6e2f7;
        border-radius: 8px;
        padding: 5px 8px;
        font-size: 11.5px;
        outline: none;
        background: #fff;
        transition: border-color .15s;
    }

    #canvas-root .cv-sidebar .sb-search:focus {
        border-color: var(--cv-primary);
    }

    #canvas-root .sb-filter-tabs {
        display: flex;
        gap: 3px;
        background: #ede9f8;
        padding: 2.5px;
        border-radius: 7px;
        margin-top: 6px;
    }

    #canvas-root .sb-tab {
        flex: 1;
        background: transparent;
        border: none;
        font-size: 9px;
        font-weight: 700;
        color: var(--cv-text-soft);
        padding: 3px 2px;
        border-radius: 5px;
        cursor: pointer;
        transition: all .15s;
        text-align: center;
        white-space: nowrap;
    }

    #canvas-root .sb-tab.active {
        background: #fff;
        color: var(--cv-primary);
        box-shadow: 0 1px 3px rgba(124, 58, 237, 0.15);
    }

    #canvas-root .cv-sidebar .sb-list {
        flex: 1;
        overflow-y: auto;
        padding: 6px 8px 12px;
    }

    #canvas-root .cat-item {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 6px 8px;
        border-radius: 8px;
        margin-bottom: 5px;
        cursor: grab;
        background: #fff;
        border: 1px solid #ede8fb;
        transition: all .15s;
    }

    #canvas-root .cat-item:hover {
        background: var(--cv-primary-soft);
        border-color: #ddd2fb;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.1);
    }

    #canvas-root .cat-item.placed {
        opacity: .5;
        background: #f8f7fc;
        border-color: #e8e4f5;
        cursor: not-allowed;
    }

    #canvas-root .cat-item.placed:hover {
        transform: none;
        box-shadow: none;
        background: #f8f7fc;
    }

    #canvas-root .cat-item .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex: none;
    }

    #canvas-root .cat-item .info {
        min-width: 0;
        flex: 1;
    }

    #canvas-root .cat-item .info .nm {
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--cv-text);
    }

    #canvas-root .cat-item .info .sub {
        font-size: 9px;
        color: var(--cv-text-soft);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #canvas-root .cat-item .badge-status {
        font-size: 8.5px;
        padding: 1.5px 5px;
        border-radius: 4px;
        font-weight: 700;
        margin-left: auto;
        flex-shrink: 0;
    }

    #canvas-root .cat-item .badge-placed {
        background: #ede9fe;
        color: #6d28d9;
    }

    #canvas-root .cat-item .badge-unplaced {
        background: #dcfce7;
        color: #15803d;
    }

    /* Legend */
    #canvas-root .cv-legend {
        position: absolute;
        left: calc(var(--cv-sidebar-w) + 12px);
        bottom: 12px;
        z-index: 40;
        background: var(--cv-card);
        border-radius: var(--cv-radius);
        box-shadow: var(--cv-shadow);
        padding: 7px 12px;
        display: flex;
        gap: 11px;
        font-size: 10.5px;
        color: var(--cv-text-soft);
        align-items: center;
    }

    #canvas-root .cv-legend .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }

    /* Viewport / world */
    #canvas-root .cv-viewport {
        position: absolute;
        top: var(--cv-toolbar-h);
        left: var(--cv-sidebar-w);
        right: 0;
        bottom: 0;
        overflow: hidden;
        background-color: var(--cv-bg);
        background-image: radial-gradient(var(--cv-dot) 1.5px, transparent 1.5px);
        background-size: 20px 20px;
        cursor: grab;
    }

    #canvas-root .cv-viewport.panning {
        cursor: grabbing;
    }

    #canvas-root .cv-viewport.dropok {
        outline: 2px dashed var(--cv-primary);
        outline-offset: -6px;
    }

    #canvas-root .cv-world {
        position: absolute;
        top: 0;
        left: 0;
        width: 2600px;
        height: 1500px;
        transform-origin: 0 0;
        will-change: transform;
    }

    #canvas-root svg.cv-edges {
        position: absolute;
        top: 0;
        left: 0;
        width: 2600px;
        height: 1500px;
        overflow: visible;
    }

    #canvas-root .cv-edge-hit {
        fill: none;
        stroke: transparent;
        stroke-width: 16;
        pointer-events: stroke;
        cursor: pointer;
    }

    #canvas-root .cv-edge-path {
        fill: none;
        stroke: #c3bfe0;
        stroke-width: 2.2;
        pointer-events: none;
        transition: stroke .12s, stroke-width .12s;
    }

    #canvas-root .cv-edge-path.active {
        stroke: var(--cv-primary);
        stroke-width: 3;
    }

    #canvas-root .cv-edge-path.selected {
        stroke: #ff8a3d;
        stroke-width: 3;
    }

    #canvas-root .cv-edge-btn circle {
        fill: #2b2140;
        opacity: .92;
        cursor: pointer;
    }

    #canvas-root .cv-edge-btn:hover circle {
        fill: var(--cv-primary);
    }

    #canvas-root .cv-edge-btn text {
        fill: #fff;
        font-size: 11px;
        text-anchor: middle;
        dominant-baseline: central;
        pointer-events: none;
    }

    #canvas-root #cvTempConnector {
        fill: none;
        stroke: var(--cv-primary);
        stroke-width: 2.4;
        stroke-dasharray: 6 5;
        pointer-events: none;
    }

    /* Node */
    #canvas-root .cv-node {
        position: absolute;
        width: 196px;
        border-radius: var(--cv-radius);
        background: var(--cv-card);
        box-shadow: var(--cv-shadow);
        cursor: pointer;
        border: 1.5px solid transparent;
    }

    #canvas-root .cv-node.selected {
        border-color: var(--cv-primary);
        box-shadow: 0 0 0 4px var(--cv-primary-soft), var(--cv-shadow);
    }

    #canvas-root .cv-node.dragging {
        box-shadow: var(--cv-shadow-lg);
        z-index: 35;
    }

    #canvas-root .cv-node .head {
        padding: 7px 10px;
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        border-radius: var(--cv-radius) var(--cv-radius) 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    #canvas-root .cv-node .head .role-tag {
        font-size: 9px;
        font-weight: 700;
        background: rgba(255, 255, 255, .28);
        padding: 2px 6px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    #canvas-root .cv-node .head .cv-node-remove {
        cursor: pointer;
        font-size: 15px;
        opacity: .75;
        transition: opacity .15s, transform .15s;
        line-height: 1;
        padding: 0 4px;
        border-radius: 4px;
        color: #fff;
    }

    #canvas-root .cv-node .head .cv-node-remove:hover {
        opacity: 1;
        background: rgba(255, 255, 255, .25);
        transform: scale(1.15);
    }

    #canvas-root .cv-node .body {
        padding: 8px 10px 10px;
    }

    #canvas-root .cv-node .body .meta {
        font-size: 10px;
        color: var(--cv-text-soft);
    }

    #canvas-root .cv-node .body .meta code {
        background: #f2effc;
        padding: 1px 5px;
        border-radius: 5px;
        font-size: 9.5px;
    }

    #canvas-root .cv-node .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 9.5px;
        font-weight: 700;
        border-radius: 20px;
        padding: 2px 8px;
        margin-top: 6px;
    }

    #canvas-root .cv-node .status-ready {
        background: #dcfce7;
        color: #166534;
    }

    #canvas-root .cv-node .status-empty {
        background: #fef3c7;
        color: #92400e;
    }

    /* Environment-specific Node Themes */
    #canvas-root .env-epicor {
        border-color: #93c5fd;
    }
    #canvas-root .env-epicor .head {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
    }
    #canvas-root .env-epicor .env-badge {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    #canvas-root .env-accurate-sp {
        border-color: #fca5a5;
    }
    #canvas-root .env-accurate-sp .head {
        background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
    }
    #canvas-root .env-accurate-sp .env-badge {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    #canvas-root .env-accurate-aja {
        border-color: #86efac;
    }
    #canvas-root .env-accurate-aja .head {
        background: linear-gradient(135deg, #16a34a, #15803d) !important;
    }
    #canvas-root .env-accurate-aja .env-badge {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    #canvas-root .cv-node .env-badge {
        font-size: 9px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
        display: inline-block;
        letter-spacing: .2px;
    }

    #canvas-root .role-approver .head {
        background: linear-gradient(135deg, var(--cv-approver), var(--cv-approver-dark));
    }

    #canvas-root .role-start .head {
        background: linear-gradient(135deg, var(--cv-neutral), var(--cv-neutral-dark));
    }

    #canvas-root .cv-handle {
        position: absolute;
        width: 19px;
        height: 19px;
        border-radius: 50%;
        background: var(--cv-primary);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        box-shadow: 0 3px 8px rgba(90, 40, 180, .35);
        opacity: 0;
        pointer-events: none;
        transition: opacity .12s, transform .12s;
        z-index: 20;
        cursor: crosshair;
    }

    #canvas-root .cv-node:hover .cv-handle {
        opacity: 1;
        pointer-events: auto;
    }

    #canvas-root .cv-handle:hover {
        transform: scale(1.18);
        background: var(--cv-primary-dark);
    }

    #canvas-root .cv-handle.top {
        top: -9px;
        left: 50%;
        margin-left: -9px;
    }

    #canvas-root .cv-handle.bottom {
        bottom: -9px;
        left: 50%;
        margin-left: -9px;
    }

    #canvas-root .cv-handle.left {
        left: -9px;
        top: 50%;
        margin-top: -9px;
    }

    #canvas-root .cv-handle.right {
        right: -9px;
        top: 50%;
        margin-top: -9px;
    }

    #canvas-root .cv-toast {
        position: absolute;
        bottom: 14px;
        left: calc(50% + var(--cv-sidebar-w)/2);
        transform: translateX(-50%) translateY(16px);
        background: #2b2140;
        color: #fff;
        padding: 8px 16px;
        border-radius: 9px;
        font-size: 12px;
        box-shadow: var(--cv-shadow-lg);
        opacity: 0;
        transition: all .2s;
        z-index: 80;
        pointer-events: none;
    }

    #canvas-root .cv-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    /* Org-tree modal reuses the modern-card style already defined in the
       page's global CSS (.modern-card, .card-header-gradient, .step-seq-badge,
       .avatar-wrapper, .info-wrapper, .emp-name, .emp-title, .role-badge,
       .node-user-footer, .card-actions, .action-btn, .btn-add/.btn-users/
       .btn-edit/.btn-delete, .karyawan-item, .table-loading, .spinner …) so
       nothing is duplicated here.
    */
    /* Multiplayer / Live Collaboration Styles */
    #canvas-root .cv-collab-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.15);
        padding: 3px 8px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }
    #canvas-root .cv-collab-bar .pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.4);
        animation: cvPulse 1.8s infinite;
    }
    @keyframes cvPulse {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6); }
        70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    #canvas-root .cv-collab-bar .avatars-group {
        display: flex;
        align-items: center;
        margin-right: 2px;
    }
    #canvas-root .cv-collab-bar .collab-avatar {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #fff;
        margin-left: -5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        cursor: default;
    }
    #canvas-root .cv-collab-bar .collab-avatar:first-child {
        margin-left: 0;
    }
    #canvas-root .cv-collab-bar .collab-count {
        font-size: 10.5px;
        font-weight: 600;
        color: #fff;
    }

    /* Live Cursor Element on Canvas */
    .cv-remote-cursor {
        position: absolute;
        top: 0;
        left: 0;
        pointer-events: none;
        z-index: 90;
        transition: transform 0.05s linear, opacity 0.3s ease;
        will-change: transform;
    }
    .cv-remote-cursor svg {
        display: block;
        filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));
    }
    .cv-remote-cursor .cursor-label {
        position: absolute;
        left: 14px;
        top: 10px;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 12px;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        letter-spacing: .2px;
    }
    
    /* Highlight node when remote user is dragging/selecting it */
    .cv-node.remote-active {
        box-shadow: 0 0 0 3px var(--remote-user-color, #ec4899), var(--cv-shadow-lg) !important;
        transition: box-shadow 0.15s;
    }
    .cv-node .remote-user-badge {
        position: absolute;
        top: -18px;
        right: 0;
        font-size: 9px;
        font-weight: 700;
        color: #fff;
        padding: 1px 6px;
        border-radius: 4px;
        pointer-events: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .org-wrapper-canvas {
        border: 1.5px solid #e2d9f3;
        border-radius: .5rem;
        overflow: auto;
        min-height: 260px;
        text-align: center;
        padding: 1.5rem 1rem;
    }
</style>

<!-- Load Pusher & Laravel Echo for Real-time Collaboration -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

<div id="canvas-root">
    <div class="cv-toolbar">
        <div class="cv-brand">Workflow Canvas &amp; Dependency</div>
        <div class="cv-hint d-none d-lg-flex">
            <span><b>Seret node</b> pindah posisi</span>
            <span><b>Klik 2x</b> buka struktur approval</span>
            <span><b>Hover → seret "+"</b> buat dependency</span>
        </div>
        <div class="cv-actions">
            <div class="cv-collab-bar" id="cvCollabBar" title="Kolaborator aktif di kanvas">
                <span class="pulse-dot"></span>
                <div class="avatars-group" id="cvCollabAvatars"></div>
                <span class="collab-count" id="cvCollabCount">1 Online</span>
            </div>
            <button id="cvAddSubmenuBtn"><i class="mdi mdi-plus me-1"></i>Sub-Menu Baru</button>
            <button class="cv-icon" id="cvZoomOut" title="Perkecil">−</button>
            <span class="cv-zoomlabel" id="cvZoomLabel">100%</span>
            <button class="cv-icon" id="cvZoomIn" title="Perbesar">+</button>
            <button id="cvResetView">Reset</button>
        </div>
    </div>

    <div class="cv-sidebar">
        <div class="sb-head">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <h4>Katalog Sub-Menu</h4>
                <span class="badge bg-primary-subtle text-primary" id="cvCatalogCountBadge" style="font-size:9.5px;font-weight:700;">0 item</span>
            </div>
            <input class="sb-search" id="cvCatalogSearch" placeholder="Cari sub-menu…" />
            <div class="sb-filter-tabs">
                <button type="button" class="sb-tab active" data-filter="all">Semua (<span id="countAll">0</span>)</button>
                <button type="button" class="sb-tab" data-filter="unplaced">Belum (<span id="countUnplaced">0</span>)</button>
                <button type="button" class="sb-tab" data-filter="placed">Di Kanvas (<span id="countPlaced">0</span>)</button>
            </div>
        </div>
        <div class="sb-list" id="cvCatalogList"></div>
    </div>

    <div class="cv-legend">
        <span><span class="dot" style="background:#2563eb"></span> Epicor ERP SPS</span>
        <span><span class="dot" style="background:#dc2626"></span> Accurate Online SP</span>
        <span><span class="dot" style="background:#16a34a"></span> Accurate Online AJA</span>
        <span class="ms-1 me-1 text-muted">|</span>
        <span><i class="mdi mdi-checkbox-marked" style="color:#16a34a"></i> Org Tree Ready</span>
        <span><i class="mdi mdi-checkbox-blank-outline" style="color:#b45309"></i> Belum Ada</span>
    </div>

    <div class="cv-viewport" id="cvViewport">
        <div class="cv-world" id="cvWorld">
            <svg class="cv-edges" id="cvEdgesSvg">
                <defs>
                    <marker id="cvArrow" viewBox="0 0 10 10" refX="8.5" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
                        <path d="M0,0 L10,5 L0,10 z" fill="#c3bfe0"></path>
                    </marker>
                    <marker id="cvArrowActive" viewBox="0 0 10 10" refX="8.5" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
                        <path d="M0,0 L10,5 L0,10 z" fill="#7c3aed"></path>
                    </marker>
                    <marker id="cvArrowSelected" viewBox="0 0 10 10" refX="8.5" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
                        <path d="M0,0 L10,5 L0,10 z" fill="#ff8a3d"></path>
                    </marker>
                    <marker id="cvArrowRev" viewBox="0 0 10 10" refX="1.5" refY="5" markerWidth="8" markerHeight="8" orient="auto">
                        <path d="M10,0 L0,5 L10,10 z" fill="#c3bfe0"></path>
                    </marker>
                    <marker id="cvArrowRevActive" viewBox="0 0 10 10" refX="1.5" refY="5" markerWidth="8" markerHeight="8" orient="auto">
                        <path d="M10,0 L0,5 L10,10 z" fill="#7c3aed"></path>
                    </marker>
                    <marker id="cvArrowRevSelected" viewBox="0 0 10 10" refX="1.5" refY="5" markerWidth="8" markerHeight="8" orient="auto">
                        <path d="M10,0 L0,5 L10,10 z" fill="#ff8a3d"></path>
                    </marker>
                </defs>
            </svg>
        </div>
    </div>

    <div class="cv-toast" id="cvToast"></div>
</div>

{{-- ============================ MODAL: Quick add sub-menu ============================ --}}
<div class="modal fade" id="modal_cv_add_submenu" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sub-Menu Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Sub-Menu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cv_submenu_nama" placeholder="Contoh: Approval Lembur">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Menu Induk <span class="text-danger">*</span></label>
                        <select class="form-control" id="cv_submenu_parent_menu">
                            <option value="">Pilih Menu Induk...</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-sm btn-primary" id="cv_btn_save_submenu">
                    <i class="mdi mdi-content-save me-1"></i>Buat &amp; Tempatkan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================ MODAL: Choose / Reassign flow ============================ --}}
<div class="modal fade" id="modal_cv_choose_flow" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cv_choose_flow_title">Atur Approval Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cv_cf_submenu_id">
                <div class="alert alert-light border mb-3" style="font-size:12.5px">
                    Sub-menu: <strong id="cv_cf_submenu_label">—</strong><br>
                    <span id="cv_cf_current_label" class="text-muted"></span>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cv_cf_mode" id="cv_cf_mode_existing" value="existing" checked>
                            <label class="form-check-label fw-semibold" for="cv_cf_mode_existing">Gunakan Approval Group dari Sub-Menu Lain</label>
                        </div>
                        <div class="text-muted mb-1" style="font-size:11.5px">Sub-menu ini akan ikut memakai flow &amp; struktur step yang sudah ada — tidak perlu susun ulang.</div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cv_cf_mode" id="cv_cf_mode_new" value="new">
                            <label class="form-check-label fw-semibold" for="cv_cf_mode_new">Buat Approval Group Baru</label>
                        </div>
                    </div>
                    <div class="col-12" id="cv_cf_reset_wrap" style="display:none">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cv_cf_mode" id="cv_cf_mode_reset" value="reset">
                            <label class="form-check-label fw-semibold" for="cv_cf_mode_reset">Reset ke Default (Hapus Keterkaitan)</label>
                        </div>
                        <div class="text-muted mb-1" style="font-size:11.5px">Sub-menu ini akan kembali ke status "Belum Ada Org Tree".</div>
                    </div>
                </div>

                <div id="cv_cf_panel_existing" class="mb-2">
                    <label class="form-label fw-semibold">Pilih Approval Group <span class="text-danger">*</span></label>
                    <select class="form-control" id="cv_cf_existing_flow_id">
                        <option value="">Pilih approval group...</option>
                    </select>
                </div>
                <div id="cv_cf_panel_new" class="mb-2 d-none">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Group Approval <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cv_flow_nama" placeholder="Contoh: Approval Revisi Harga PO">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control" id="cv_flow_deskripsi" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-sm btn-primary" id="cv_btn_save_flow">
                    <i class="mdi mdi-content-save me-1"></i>Simpan &amp; Lanjut Susun Step
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================ MODAL: Org Approval Tree (single flow) ============================ --}}
<div class="modal fade" id="modal_cv_org_tree" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="cv_org_tree_title">Struktur Persetujuan</h5>
                    <small class="text-muted" id="cv_org_tree_subtitle"></small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary" id="cv_btn_reassign_flow" title="Ganti/atur ulang approval group untuk sub-menu ini">
                        <i class="mdi mdi-swap-horizontal me-1"></i>Ganti Approval Group
                    </button>
                    <button class="btn btn-sm btn-outline-primary" id="cv_btn_add_root_step">
                        <i class="mdi mdi-plus me-1"></i>Tambah Step Pertama
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body position-relative" style="min-height:300px">
                <div id="cvOrgTreeLoading" class="table-loading d-none">
                    <div class="loading-content">
                        <div class="spinner"></div><span>Memuat data...</span>
                    </div>
                </div>
                <div id="cv-org-tree-container" class="org-wrapper-canvas"></div>
            </div>
        </div>
    </div>
</div>

{{-- ============================ MODAL: Add / Edit Step (reused pattern) ============================ --}}
<div class="modal fade" id="modal_cv_add_step" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="cv_modal_step_title">Tambah Step</h5>
                    <small class="text-muted" id="cv_modal_step_subtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cv_step_id_edit">
                <input type="hidden" id="cv_step_flow_id">
                <input type="hidden" id="cv_step_parent_id">
                <div id="cv_parent_step_info" class="alert alert-light border mb-3 d-none">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-arrow-up-thick text-primary fs-5"></i>
                        <div>
                            <div class="fw-bold" style="font-size:13px">Atasan Langsung</div>
                            <div class="text-muted" style="font-size:12px" id="cv_parent_step_name_info">—</div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Step <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cv_step_nama" placeholder="Contoh: Approval SPV Purchasing">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Holding</label>
                        <select class="form-control" id="cv_step_holding_id">
                            <option value="">Pilih Holding...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span></label>
                        <select class="form-control" id="cv_step_departemen_id">
                            <option value="">Pilih Departemen...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Level Jabatan <span class="text-danger">*</span></label>
                        <select class="form-control" id="cv_step_jabatan_id">
                            <option value="">Pilih Level Jabatan...</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-light border d-flex align-items-start gap-2 mb-0">
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" id="cv_step_is_checker" name="is_checker">
                            </div>
                            <div>
                                <label class="form-check-label fw-semibold mb-0" for="cv_step_is_checker">
                                    Jadikan Checker untuk flow ini
                                </label>
                                <div class="text-muted" style="font-size:11px">
                                    Dipakai sebagai pemeriksa saat tiket di-generate dari dependency. Hanya 1 step per flow.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="cv_btn_save_step" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-content-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_cv_edit_step" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Step</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cv_edit_step_id">
                <input type="hidden" id="cv_edit_step_flow_id">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Step <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cv_edit_step_nama">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Atasan Langsung (Parent Step)</label>
                        <select class="form-control" id="cv_edit_step_parent_id">
                            <option value="">-- Sebagai Step Pertama (Root / Tidak Ada Atasan) --</option>
                        </select>
                        <small class="text-muted" style="font-size:11px">Ubah atasan langsung untuk memindahkan posisi step ini dalam struktur approval.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Holding</label>
                        <select class="form-control" id="cv_edit_step_holding_id">
                            <option value="">Pilih Holding...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span></label>
                        <select class="form-control" id="cv_edit_step_departemen_id">
                            <option value="">Pilih Departemen...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Level Jabatan <span class="text-danger">*</span></label>
                        <select class="form-control" id="cv_edit_step_jabatan_id">
                            <option value="">Pilih Level Jabatan...</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-light border d-flex align-items-start gap-2 mb-0">
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" id="cv_edit_step_is_checker" name="is_checker">
                            </div>
                            <div>
                                <label class="form-check-label fw-semibold mb-0" for="cv_edit_step_is_checker">
                                    Jadikan Checker untuk flow ini
                                </label>
                                <div class="text-muted" style="font-size:11px">
                                    Dipakai sebagai pemeriksa saat tiket di-generate dari dependency. Hanya 1 step per flow.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="cv_btn_update_step" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-content-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================ MODAL: Ganti Parent Step (Quick Reparent) ============================ --}}
<div class="modal fade" id="modal_cv_change_parent" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Ganti Atasan (Parent Step)</h5>
                    <small class="text-muted" id="cv_change_parent_subtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cv_cp_step_id">
                <div class="alert alert-light border py-2 px-3 d-flex align-items-center gap-2 mb-3" style="font-size:12.5px">
                    <i class="mdi mdi-sitemap text-primary fs-4"></i>
                    <div>
                        Pilih atasan baru untuk memindahkan posisi step ini dalam struktur approval.
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Step yang Dipindahkan</label>
                    <input type="text" class="form-control bg-light" id="cv_cp_step_name" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Atasan Baru (Parent Step)</label>
                    <select class="form-control" id="cv_cp_new_parent_id">
                        <option value="">-- Sebagai Step Pertama (Root / Tidak Ada Atasan) --</option>
                    </select>
                    <small class="text-muted mt-1 d-block" style="font-size:11px">
                        Pilih <em>"-- Sebagai Step Pertama (Root) --"</em> jika ingin step ini berada di tingkat paling atas.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="cv_btn_save_change_parent" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-check me-1"></i>Simpan Struktur
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================ MODAL: Pilih Karyawan (reused pattern) ============================ --}}
<div class="modal fade" id="modal_cv_pilih_karyawan" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title mb-0 fw-semibold"><i class="mdi mdi-account-multiple me-1 text-primary"></i>Pilih Karyawan</h5>
                    <small class="text-muted" id="cv_modal_karyawan_label"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="px-3 pt-3 pb-2 bg-white border-bottom sticky-top" style="z-index:5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                        <input type="text" id="cv_search_karyawan" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama, divisi, atau bagian...">
                    </div>
                </div>
                <div class="px-3 py-2 bg-light border-bottom d-flex align-items-center sticky-top" style="top:52px;z-index:4">
                    <div class="form-check m-0">
                        <input type="checkbox" class="form-check-input" id="cv_check_all_karyawan">
                        <label class="form-check-label small text-muted" for="cv_check_all_karyawan">Pilih semua yang ditampilkan</label>
                    </div>
                    <span id="cv-selected-count" class="badge bg-primary-subtle text-primary ms-auto d-none"></span>
                </div>
                <div id="cv-karyawan-list" style="max-height: 420px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"><i class="mdi mdi-close me-1"></i>Tutup</button>
                <button type="button" id="cv_btn_save_karyawan" class="btn btn-sm btn-primary"><i class="mdi mdi-content-save me-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================ MODAL: Dependency (edge condition) ============================ --}}
<div class="modal fade" id="modal_cv_dependency" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cv_modal_dep_title">Dependency Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cv_dep_rule_id">
                <input type="hidden" id="cv_dep_source">
                <input type="hidden" id="cv_dep_target">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="alert alert-light border mb-0 d-flex align-items-center gap-2" style="font-size:12.5px">
                            <span id="cv_dep_source_label" class="fw-semibold">—</span>
                            <i class="mdi mdi-arrow-right"></i>
                            <span id="cv_dep_target_label" class="fw-semibold">—</span>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch bg-light p-3 rounded border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="cv_dep_is_cross_env" value="1">
                            <label class="form-check-label fw-semibold text-dark mb-0" for="cv_dep_is_cross_env" style="cursor:pointer;">
                                <i class="mdi mdi-transfer me-1 text-primary"></i>Cross-Environment (Pecah Menjadi Grup Tiket Baru di Sistem Target / Non-Blocking)
                            </label>
                            <div class="text-muted mt-1 ms-4" style="font-size:11px;">
                                Jika aktif, tiket di sistem tujuan dibuat sebagai grup mandiri (Root Baru) dan tidak memblokir approval/validasi tiket asal.
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch bg-light p-3 rounded border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="cv_dep_is_conditional" value="1">
                            <label class="form-check-label fw-semibold text-dark mb-0" for="cv_dep_is_conditional" style="cursor:pointer;">
                                Sifat Kondisional (Memerlukan Pilihan / Toggle User) user diwajibkan memilih kondisi pada saat pembuatan ticket ubah hapus.
                            </label>
                            <div class="text-muted mt-1 ms-4" style="font-size:11px;">
                                Jika diaktifkan akan muncul pada halaman tambah ticket ubah hapus dan halaman pengisian data generate ticket baru.
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch bg-light p-3 rounded border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="cv_dep_is_bidirectional" value="1">
                            <label class="form-check-label fw-semibold text-dark mb-0" for="cv_dep_is_bidirectional" style="cursor:pointer;">
                                Izinkan Pengecekan Alur Mundur / Revisi (Bidirectional 2-Arah) untuk memungkinkan user melakukan revisi transaksi alur maju atau mundur.
                            </label>
                            <div class="text-muted mt-1 ms-4" style="font-size:11px;">
                                Jika diaktifkan maka pada saat membuat ticket baru akan diberikan pilihan untuk memilih opsi "Apakah transaksi sebelumnya perlu direvisi?".
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Keterangan / Pertanyaan</label>
                        <input type="text" class="form-control" id="cv_dep_description" placeholder="Contoh: Apakah perlu merevisi transaksi di Accurate Online AJA?">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-sm btn-primary" id="cv_btn_save_dependency">
                    <i class="mdi mdi-content-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        (function($) {
            if (typeof $ === 'undefined') {
                console.error('jQuery not ready for canvas');
                return;
            }

            const BASE = "{{ url('master_ticket') }}";
            const TOKEN = "{{ csrf_token() }}";
            const NODE_H = 74;

            function showToast(msg) {
                const t = document.getElementById('cvToast');
                t.textContent = msg;
                t.classList.add('show');
                clearTimeout(t._timer);
                t._timer = setTimeout(() => t.classList.remove('show'), 2200);
            }

            function swalLoading() {
                Swal.fire({
                    allowOutsideClick: false,
                    background: 'transparent',
                    html: '<div class="spinner-grow text-primary spinner-grow-sm me-2"></div><div class="spinner-grow text-primary spinner-grow-sm me-2"></div><div class="spinner-grow text-primary spinner-grow-sm me-2"></div>',
                    showCancelButton: false,
                    showConfirmButton: false
                });
            }

            /* ============================================================
               Canvas state (loaded from server)
               ============================================================ */
            let nodes = []; // {id, sub_menu_id, name, menu_name, x, y, w, has_org_tree, flow_id}
            let edges = []; // {id, from, to, condition_key, condition_label, bidirectional, description}
            let catalog = []; // all sub-menus (placed + not placed) for the sidebar

            const world = document.getElementById('cvWorld');
            const edgesSvg = document.getElementById('cvEdgesSvg');
            const viewport = document.getElementById('cvViewport');
            const nodeEls = {};

            function byIdMap() {
                return Object.fromEntries(nodes.map(n => [n.id, n]));
            }

            function nodeRect(n) {
                const el = nodeEls[n.id];
                return {
                    x: n.x,
                    y: n.y,
                    w: n.w || 196,
                    h: el ? el.offsetHeight : NODE_H
                };
            }

            function loadCanvas() {
                $.get(`${BASE}/canvas/data`, function(res) {
                    nodes = (res.nodes || []).map(n => ({
                        id: n.id,
                        sub_menu_id: n.sub_menu_id,
                        name: n.name,
                        menu_name: n.menu_name,
                        kategori: parseInt(n.kategori) || 1,
                        kategori_name: n.kategori_name || 'Epicor ERP',
                        x: n.x || 50,
                        y: n.y || 50,
                        w: 196,
                        has_org_tree: !!n.has_org_tree,
                        flow_id: n.flow_id,
                        flow_name: n.flow_name,
                        is_on_canvas: !!n.is_on_canvas,
                    }));
                    edges = (res.edges || []).map(e => ({
                        id: e.id,
                        from: e.from,
                        to: e.to,
                        condition_key: e.condition_key,
                        condition_label: e.condition_label,
                        bidirectional: !!e.bidirectional,
                        is_conditional: !!e.is_conditional,
                        is_cross_environment: !!e.is_cross_environment,
                        description: e.description,
                    }));
                    catalog = nodes.map(n => ({
                        sub_menu_id: n.id,
                        name: n.name,
                        menu_name: n.menu_name,
                        kategori: n.kategori,
                        kategori_name: n.kategori_name,
                        placed: n.is_on_canvas
                    }));

                    world.querySelectorAll('.cv-node').forEach(el => el.remove());
                    Object.keys(nodeEls).forEach(k => delete nodeEls[k]);
                    nodes.filter(n => n.is_on_canvas).forEach(createNodeEl);
                    rebuildEdgeEls();
                    redrawEdges();
                    renderCatalog();
                }).fail(function() {
                    showToast('Gagal memuat data canvas');
                });
            }

            /* ============================================================
               Node rendering & Environment Helpers
               ============================================================ */
            function getEnvClass(kategori) {
                if (kategori === 2) return 'env-accurate-sp';
                if (kategori === 3) return 'env-accurate-aja';
                return 'env-epicor';
            }

            function createNodeEl(n) {
                const el = document.createElement('div');
                const envClass = getEnvClass(n.kategori);
                el.className = `cv-node ${envClass} role-` + (n.has_org_tree ? 'approver' : 'start');
                el.style.left = n.x + 'px';
                el.style.top = n.y + 'px';
                el.style.width = (n.w || 196) + 'px';
                el.dataset.id = n.id;
                const statusChip = n.has_org_tree ?
                    '<span class="status-chip status-ready"><i class="mdi mdi-checkbox-marked"></i> Org Tree Ready</span>' :
                    '<span class="status-chip status-empty"><i class="mdi mdi-checkbox-blank-outline"></i> Belum Ada Org Tree</span>';
                el.innerHTML = `
            <div class="head">
                <span>${n.name}</span>
                <span class="d-flex align-items-center gap-1">
                    <span class="role-tag">${n.has_org_tree?'Ready':'Baru'}</span>
                    <span class="cv-node-remove" title="Kembalikan ke sidebar">×</span>
                </span>
            </div>
            <div class="body">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="env-badge">${n.kategori_name || 'Epicor ERP'}</span>
                </div>
                <div class="meta">Menu: <code>${n.menu_name||'-'}</code></div>
                <div>${statusChip}</div>
            </div>
            <div class="cv-handle top" data-side="top">+</div>
            <div class="cv-handle bottom" data-side="bottom">+</div>
            <div class="cv-handle left" data-side="left">+</div>
            <div class="cv-handle right" data-side="right">+</div>
        `;
                world.appendChild(el);
                nodeEls[n.id] = el;
                wireNodeInteractions(n.id, el);

                const removeBtn = el.querySelector('.cv-node-remove');
                if (removeBtn) {
                    removeBtn.addEventListener('mousedown', (ev) => ev.stopPropagation());
                    removeBtn.addEventListener('click', (ev) => {
                        ev.stopPropagation();
                        Swal.fire({
                            title: 'Kembalikan ke Sidebar?',
                            text: `"${n.name}" akan dilepas dari kanvas. Dependency rule dan org tree yang sudah ada TIDAK dihapus — node ini bisa ditarik ke kanvas lagi kapan saja dari katalog sidebar.`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, kembalikan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#7c3aed'
                        }).then(r => {
                            if (!r.isConfirmed) return;
                            $.post(`${BASE}/canvas/node/unplace/${n.id}`, {
                                _token: TOKEN
                            }, function() {
                                n.is_on_canvas = false;
                                el.remove();
                                delete nodeEls[n.id];
                                if (selectedNodeId === n.id) selectNode(null);
                                rebuildEdgeEls();
                                redrawEdges();
                                const cat = catalog.find(c => c.sub_menu_id === n.id);
                                if (cat) cat.placed = false;
                                renderCatalog();
                                broadcastCanvasRefresh();
                                showToast(`"${n.name}" dikembalikan ke sidebar`);
                            }).fail(() => showToast('Gagal mengembalikan node ke sidebar'));
                        });
                    });
                }

                return el;
            }

            function refreshNodeStatus(subMenuId, hasOrgTree, flowId, flowName) {
                const n = byIdMap()[subMenuId];
                if (!n) return;
                n.has_org_tree = hasOrgTree;
                n.flow_id = flowId;
                n.flow_name = flowName;
                const el = nodeEls[subMenuId];
                if (!el) return;
                const envClass = getEnvClass(n.kategori);
                el.className = `cv-node ${envClass} role-` + (hasOrgTree ? 'approver' : 'start') + (el.classList.contains('selected') ? ' selected' : '');
                el.querySelector('.role-tag').textContent = hasOrgTree ? 'Ready' : 'Baru';
                const chip = el.querySelector('.status-chip');
                chip.className = 'status-chip ' + (hasOrgTree ? 'status-ready' : 'status-empty');
                chip.innerHTML = hasOrgTree ?
                    '<i class="mdi mdi-checkbox-marked"></i> Org Tree Ready' :
                    '<i class="mdi mdi-checkbox-blank-outline"></i> Belum Ada Org Tree';
            }

            /* ============================================================
               Orthogonal edge routing (obstacle-aware) — same technique as the
               canvas prototype, adapted to this node size.
               ============================================================ */
            function buildPath(a, b, all, laneOffset) {
                laneOffset = laneOffset || 0;
                const ra = nodeRect(a), rb = nodeRect(b);
                const margin = 22, minGap = 40, stub = 28;
                const obstacles = all.filter(n => n.id !== a.id && n.id !== b.id).map(nodeRect);
                const aCy = ra.y + ra.h / 2, bCy = rb.y + rb.h / 2;

                const gapRight = rb.x - (ra.x + ra.w);   // b di kanan a
                const gapLeft  = ra.x - (rb.x + rb.w);   // b di kiri a

                const hitsObstacle = (x, y1, y2) => obstacles.find(o =>
                    x > o.x - margin && x < o.x + o.w + margin &&
                    Math.max(y1, y2) > o.y - margin && Math.min(y1, y2) < o.y + o.h + margin);

                /* ---------- HORIZONTAL: kiri->kanan atau kanan->kiri ---------- */
                if (gapRight >= minGap || gapLeft >= minGap) {
                    const dir = gapRight >= minGap ? 1 : -1;
                    const start = { x: dir === 1 ? ra.x + ra.w : ra.x,  y: aCy + laneOffset * 0.35 };
                    const end   = { x: dir === 1 ? rb.x : rb.x + rb.w,   y: bCy + laneOffset * 0.35 };

                    let midX = (start.x + end.x) / 2 + laneOffset;
                    let tries = 0, blocked = hitsObstacle(midX, start.y, end.y);
                    while (blocked && tries < 6) {
                        const before = blocked.x - margin, after = blocked.x + blocked.w + margin;
                        const cand = (Math.abs(before - start.x) < Math.abs(after - start.x)) ? before : after;
                        if ((cand - start.x) * dir <= 10 || (end.x - cand) * dir <= 10) {
                            const detourY = (blocked.y + blocked.h / 2 < (start.y + end.y) / 2)
                                ? blocked.y + blocked.h + margin : blocked.y - margin;
                            const y = detourY + laneOffset;
                            return [start,
                                { x: start.x + dir * stub, y: start.y },
                                { x: start.x + dir * stub, y },
                                { x: end.x - dir * stub,   y },
                                { x: end.x - dir * stub,   y: end.y },
                                end];
                        }
                        midX = cand;
                        blocked = hitsObstacle(midX, start.y, end.y);
                        tries++;
                    }
                    if (Math.abs(start.y - end.y) < 1) return [start, end];
                    return [start, { x: midX, y: start.y }, { x: midX, y: end.y }, end];
                }

                /* ---------- VERTIKAL: node bertumpuk secara horizontal ---------- */
                const goingDown = bCy > aCy;
                const start = { x: ra.x + ra.w / 2 + laneOffset * 0.35, y: goingDown ? ra.y + ra.h : ra.y };
                const end   = { x: rb.x + rb.w / 2 + laneOffset * 0.35, y: goingDown ? rb.y : rb.y + rb.h };
                const midY = (start.y + end.y) / 2 + laneOffset;
                if (Math.abs(start.x - end.x) < 1) return [start, end];
                return [start, { x: start.x, y: midY }, { x: end.x, y: midY }, end];
            }

            function roundedPathD(pts, r = 9) {
                if (pts.length <= 2) return `M ${pts[0].x} ${pts[0].y} L ${pts[1].x} ${pts[1].y}`;
                let d = `M ${pts[0].x} ${pts[0].y} `;
                for (let i = 1; i < pts.length - 1; i++) {
                    const p0 = pts[i - 1],
                        p1 = pts[i],
                        p2 = pts[i + 1];
                    const v1x = p1.x - p0.x,
                        v1y = p1.y - p0.y,
                        len1 = Math.hypot(v1x, v1y) || 1;
                    const v2x = p2.x - p1.x,
                        v2y = p2.y - p1.y,
                        len2 = Math.hypot(v2x, v2y) || 1;
                    const rr = Math.min(r, len1 / 2, len2 / 2);
                    const inX = p1.x - (v1x / len1) * rr,
                        inY = p1.y - (v1y / len1) * rr;
                    const outX = p1.x + (v2x / len2) * rr,
                        outY = p1.y + (v2y / len2) * rr;
                    d += `L ${inX} ${inY} Q ${p1.x} ${p1.y} ${outX} ${outY} `;
                }
                d += `L ${pts[pts.length-1].x} ${pts[pts.length-1].y}`;
                return d;
            }

            let edgeEls = {},
                selectedEdgeId = null,
                selectedNodeId = null,
                edgeBtnGroup = null;

            function laneGroups() {
                const groups = {};
                edges.forEach(e => {
                    const key = [e.from, e.to].sort().join('|');
                    (groups[key] = groups[key] || []).push(e);
                });
                return groups;
            }

            function rebuildEdgeEls() {
                Object.values(edgeEls).forEach(o => {
                    o.hit.remove();
                    o.path.remove();
                });
                edgeEls = {};
                edges.forEach(e => {
                    const hit = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    hit.setAttribute('class', 'cv-edge-hit');
                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    path.setAttribute('class', 'cv-edge-path');
                    edgesSvg.appendChild(hit);
                    edgesSvg.appendChild(path);
                    hit.addEventListener('mousedown', (ev) => {
                        ev.stopPropagation();
                        selectEdge(e.id);
                    });
                    hit.addEventListener('dblclick', (ev) => {
                        ev.stopPropagation();
                        selectEdge(e.id);
                        const edge = edges.find(x => x.id === e.id);
                        if (edge) {
                            openDependencyModal(edge.from, edge.to, edge);
                        }
                    });
                    edgeEls[e.id] = {
                        hit,
                        path
                    };
                });
            }

            function redrawEdges() {
                const map = byIdMap();
                const groups = laneGroups();
                edges.forEach(e => {
                    const a = map[e.from],
                        b = map[e.to];
                    const els = edgeEls[e.id];
                    if (!a || !b || !els) return;
                    const key = [e.from, e.to].sort().join('|');
                    const group = groups[key];
                    const idx = group.indexOf(e);
                    const laneOffset = (idx - (group.length - 1) / 2) * 16;
                    const canonical = String(e.from) <= String(e.to);
                    const [p, q] = canonical ? [a, b] : [b, a];
                    let pts = buildPath(p, q, nodes.filter(n => n.is_on_canvas !== false), laneOffset);
                    if (!canonical) pts = pts.reverse();   // geometri sama, hanya arah panah yang terbalik
                    const d = roundedPathD(pts);
                    els.hit.setAttribute('d', d);
                    els.path.setAttribute('d', d);
                    const isActive = selectedNodeId && (e.from === selectedNodeId || e.to === selectedNodeId);
                    const isSel = e.id === selectedEdgeId;
                    els.path.classList.toggle('active', !!isActive && !isSel);
                    els.path.classList.toggle('selected', !!isSel);
                    const endMarker = isSel ? 'url(#cvArrowSelected)' : (isActive ? 'url(#cvArrowActive)' : 'url(#cvArrow)');
                    const startMarker = isSel ? 'url(#cvArrowRevSelected)' : (isActive ? 'url(#cvArrowRevActive)' : 'url(#cvArrowRev)');
                    els.path.setAttribute('marker-end', endMarker);
                    if (e.bidirectional) els.path.setAttribute('marker-start', startMarker);
                    else els.path.removeAttribute('marker-start');
                });
                positionEdgeButtons();
            }

            function positionEdgeButtons() {
                if (edgeBtnGroup) {
                    edgeBtnGroup.remove();
                    edgeBtnGroup = null;
                }
                if (!selectedEdgeId) return;
                const els = edgeEls[selectedEdgeId];
                if (!els) return;
                const len = els.path.getTotalLength();
                const mid = els.path.getPointAtLength(len / 2);
                const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                g.innerHTML = `
            <g class="cv-edge-btn" data-act="dir" transform="translate(${mid.x-38},${mid.y-13})"><circle cx="13" cy="13" r="13"></circle><text x="13" y="14">⇄</text></g>
            <g class="cv-edge-btn" data-act="edit" transform="translate(${mid.x-10},${mid.y-13})"><circle cx="13" cy="13" r="13"></circle><text x="13" y="14">✎</text></g>
            <g class="cv-edge-btn" data-act="del" transform="translate(${mid.x+18},${mid.y-13})"><circle cx="13" cy="13" r="13"></circle><text x="13" y="14">×</text></g>`;
                edgesSvg.appendChild(g);
                edgeBtnGroup = g;
                g.querySelector('[data-act="dir"]').addEventListener('mousedown', (ev) => {
                    ev.stopPropagation();
                    $.post(`${BASE}/canvas/edge/toggle/${selectedEdgeId}`, {
                        _token: TOKEN
                    }, function(res) {
                        const e = edges.find(x => x.id === selectedEdgeId);
                        if (e) e.bidirectional = res.bidirectional;
                        redrawEdges();
                        showToast(res.bidirectional ? 'Koneksi dijadikan 2 arah' : 'Koneksi dijadikan 1 arah');
                    }).fail(() => showToast('Gagal mengubah arah koneksi'));
                });
                g.querySelector('[data-act="edit"]').addEventListener('mousedown', (ev) => {
                    ev.stopPropagation();
                    const e = edges.find(x => x.id === selectedEdgeId);
                    if (e) {
                        openDependencyModal(e.from, e.to, e);
                    }
                });
                g.querySelector('[data-act="del"]').addEventListener('mousedown', (ev) => {
                    ev.stopPropagation();
                    const edgeId = selectedEdgeId;
                    Swal.fire({
                        title: 'Hapus dependency ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#ea5455'
                    }).then(r => {
                        if (!r.isConfirmed) return;
                        $.ajax({
                            url: `${BASE}/dependency/delete/${edgeId}`,
                            type: 'POST',
                            data: {
                                _token: TOKEN,
                                _method: 'DELETE'
                            },
                            success: function() {
                                edges = edges.filter(x => x.id !== edgeId);
                                selectedEdgeId = null;
                                rebuildEdgeEls();
                                redrawEdges();
                                showToast('Koneksi dihapus');
                            },
                            error: function() {
                                showToast('Gagal menghapus koneksi');
                            }
                        });
                    });
                });
            }

            function selectEdge(id) {
                selectedEdgeId = id;
                selectedNodeId = null;
                Object.values(nodeEls).forEach(el => el.classList.remove('selected'));
                redrawEdges();
            }

            function selectNode(id) {
                selectedNodeId = id;
                selectedEdgeId = null;
                Object.values(nodeEls).forEach(el => el.classList.remove('selected'));
                if (id) nodeEls[id].classList.add('selected');
                redrawEdges();
            }

            /* ============================================================
               Pan & zoom
               ============================================================ */
            let panX = 30,
                panY = 10,
                zoom = 0.85;

            // Restore last view from localStorage if available
            try {
                const savedView = JSON.parse(localStorage.getItem('cv_view_state') || '{}');
                if (savedView && typeof savedView.panX === 'number' && typeof savedView.zoom === 'number') {
                    panX = savedView.panX;
                    panY = savedView.panY;
                    zoom = savedView.zoom;
                }
            } catch(e) {}

            function applyTransform(saveState = true) {
                world.style.transform = `translate(${panX}px,${panY}px) scale(${zoom})`;
                document.getElementById('cvZoomLabel').textContent = Math.round(zoom * 100) + '%';
                if (saveState) {
                    try {
                        localStorage.setItem('cv_view_state', JSON.stringify({ panX, panY, zoom }));
                    } catch(e) {}
                }
            }
            applyTransform(false);

            let isPanning = false,
                panStart = {
                    x: 0,
                    y: 0
                },
                panOrigin = {
                    x: 0,
                    y: 0
                };
            viewport.addEventListener('mousedown', (ev) => {
                if (ev.target.closest('.cv-node')) return;
                isPanning = true;
                viewport.classList.add('panning');
                panStart = {
                    x: ev.clientX,
                    y: ev.clientY
                };
                panOrigin = {
                    x: panX,
                    y: panY
                };
                selectNode(null);
                selectEdge(null);
                positionEdgeButtons();
            });
            window.addEventListener('mousemove', (ev) => {
                if (!isPanning) return;
                panX = panOrigin.x + (ev.clientX - panStart.x);
                panY = panOrigin.y + (ev.clientY - panStart.y);
                applyTransform();
            });
            window.addEventListener('mouseup', () => {
                isPanning = false;
                viewport.classList.remove('panning');
            });
            const MIN_ZOOM = 0.1; // Bisa zoom out sampai 10%
            const MAX_ZOOM = 2.5; // Bisa zoom in sampai 250%

            viewport.addEventListener('wheel', (ev) => {
                ev.preventDefault();
                const prevZoom = zoom;
                const zoomFactor = ev.deltaY < 0 ? 1.12 : 0.88;
                zoom = Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, +(zoom * zoomFactor).toFixed(3)));
                const rect = viewport.getBoundingClientRect();
                const cx = ev.clientX - rect.left,
                    cy = ev.clientY - rect.top;
                panX = cx - ((cx - panX) / prevZoom) * zoom;
                panY = cy - ((cy - panY) / prevZoom) * zoom;
                applyTransform();
            }, {
                passive: false
            });
            document.getElementById('cvZoomIn').onclick = () => {
                zoom = Math.min(MAX_ZOOM, +(zoom * 1.15).toFixed(2));
                applyTransform();
            };
            document.getElementById('cvZoomOut').onclick = () => {
                zoom = Math.max(MIN_ZOOM, +(zoom * 0.85).toFixed(2));
                applyTransform();
            };
            document.getElementById('cvResetView').onclick = () => {
                panX = 30;
                panY = 10;
                zoom = 0.85;
                applyTransform();
            };

            function clientToWorld(clientX, clientY) {
                const rect = viewport.getBoundingClientRect();
                return {
                    x: (clientX - rect.left - panX) / zoom,
                    y: (clientY - rect.top - panY) / zoom
                };
            }

            /* ============================================================
               Node interactions: drag / click / dbl-click / + handle connect
               ============================================================ */
            let lastClick = {
                id: null,
                time: 0
            };
            let connecting = null;
            let positionSaveTimer = null;

            function persistPosition(n) {
                clearTimeout(positionSaveTimer);
                positionSaveTimer = setTimeout(() => {
                    $.post(`${BASE}/canvas/save-position`, {
                        _token: TOKEN,
                        nodes: [{
                            id: n.id,
                            x: Math.round(n.x),
                            y: Math.round(n.y)
                        }]
                    });
                }, 250);
            }

            function wireNodeInteractions(id, el) {
                let dragging = false,
                    moved = false,
                    startX = 0,
                    startY = 0,
                    origX = 0,
                    origY = 0;

                el.addEventListener('mousedown', (ev) => {
                    if (ev.target.closest('.cv-handle') || ev.target.closest('.cv-node-remove')) return;
                    ev.stopPropagation();
                    dragging = true;
                    moved = false;
                    startX = ev.clientX;
                    startY = ev.clientY;
                    const n = byIdMap()[id];
                    origX = n.x;
                    origY = n.y;
                    el.classList.add('dragging');
                });

                window.addEventListener('mousemove', (ev) => {
                    if (!dragging) return;
                    const dx = (ev.clientX - startX) / zoom,
                        dy = (ev.clientY - startY) / zoom;
                    if (Math.abs(dx) > 4 || Math.abs(dy) > 4) moved = true;
                    if (moved) {
                        const n = byIdMap()[id];
                        n.x = origX + dx;
                        n.y = origY + dy;
                        el.style.left = n.x + 'px';
                        el.style.top = n.y + 'px';
                        redrawEdges();
                        broadcastNodeDrag(id, Math.round(n.x), Math.round(n.y));
                    }
                });

                window.addEventListener('mouseup', () => {
                    if (!dragging) return;
                    dragging = false;
                    el.classList.remove('dragging');
                    if (!moved) {
                        selectNode(id);
                        const now = Date.now();
                        if (lastClick.id === id && (now - lastClick.time) < 350) {
                            openOrgTree(byIdMap()[id]);
                            lastClick = {
                                id: null,
                                time: 0
                            };
                        } else lastClick = {
                            id,
                            time: now
                        };
                    } else {
                        persistPosition(byIdMap()[id]);
                        broadcastNodeDragEnd(id);
                    }
                });

                el.querySelectorAll('.cv-handle').forEach(h => {
                    h.addEventListener('mousedown', (ev) => {
                        ev.stopPropagation();
                        const temp = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        temp.setAttribute('id', 'cvTempConnector');
                        edgesSvg.appendChild(temp);
                        connecting = {
                            fromId: id,
                            tempPath: temp
                        };
                    });
                });
            }

            window.addEventListener('mousemove', (ev) => {
                if (!connecting) return;
                const n = byIdMap()[connecting.fromId];
                const start = {
                    x: n.x + (n.w || 196) / 2,
                    y: n.y + NODE_H / 2
                };
                const cur = clientToWorld(ev.clientX, ev.clientY);
                const midX = start.x + (cur.x - start.x) / 2;
                const d = `M ${start.x} ${start.y} L ${midX} ${start.y} L ${midX} ${cur.y} L ${cur.x} ${cur.y}`;
                connecting.tempPath.setAttribute('d', d);
            });

            window.addEventListener('mouseup', (ev) => {
                if (!connecting) return;
                const targetEl = document.elementFromPoint(ev.clientX, ev.clientY);
                const targetNodeEl = targetEl && targetEl.closest('.cv-node');
                connecting.tempPath.remove();
                if (targetNodeEl && targetNodeEl.dataset.id !== connecting.fromId) {
                    openDependencyModal(connecting.fromId, targetNodeEl.dataset.id);
                }
                connecting = null;
            });

            /* ============================================================
               Sidebar catalog: filter tabs + search + drag & drop onto canvas
               ============================================================ */
            const catalogListEl = document.getElementById('cvCatalogList');
            let currentFilterTab = 'all'; // 'all', 'unplaced', 'placed'

            function updateCatalogCounts() {
                const total = catalog.length;
                const placed = catalog.filter(c => c.placed).length;
                const unplaced = total - placed;
                $('#countAll').text(total);
                $('#countUnplaced').text(unplaced);
                $('#countPlaced').text(placed);
                $('#cvCatalogCountBadge').text(`${unplaced} belum`);
            }

            function renderCatalog(filter = null) {
                if (filter === null) {
                    filter = document.getElementById('cvCatalogSearch').value || '';
                }
                filter = (filter || '').toLowerCase();
                catalogListEl.innerHTML = '';
                updateCatalogCounts();

                const filtered = catalog.filter(c => {
                    const matchSearch = c.name.toLowerCase().includes(filter) || (c.menu_name && c.menu_name.toLowerCase().includes(filter));
                    if (!matchSearch) return false;
                    if (currentFilterTab === 'unplaced') return !c.placed;
                    if (currentFilterTab === 'placed') return c.placed;
                    return true;
                });

                if (filtered.length === 0) {
                    catalogListEl.innerHTML = '<div class="text-center py-4 text-muted" style="font-size:11px;">Tidak ada sub-menu yang cocok.</div>';
                    return;
                }

                filtered.forEach(c => {
                    const row = document.createElement('div');
                    row.className = 'cat-item' + (c.placed ? ' placed' : ' unplaced');
                    row.draggable = !c.placed;

                    const envColor = c.kategori === 2 ? '#dc2626' : (c.kategori === 3 ? '#16a34a' : '#2563eb');

                    row.innerHTML = `
                        <span class="dot" style="background:${envColor}"></span>
                        <div class="info">
                            <div class="nm" title="${c.name}">${c.name}</div>
                            <div class="sub">${c.menu_name || '-'}</div>
                        </div>
                        ${c.placed ? '<span class="badge-status badge-placed">Di Kanvas</span>' : '<span class="badge-status badge-unplaced"><i class="mdi mdi-drag me-1"></i>Tarik</span>'}
                    `;
                    if (!c.placed) {
                        row.addEventListener('dragstart', (ev) => {
                            ev.dataTransfer.setData('text/plain', c.sub_menu_id);
                            ev.dataTransfer.effectAllowed = 'copyMove';
                        });
                    }
                    catalogListEl.appendChild(row);
                });
            }

            document.getElementById('cvCatalogSearch').addEventListener('input', (ev) => renderCatalog(ev.target.value));

            $(document).on('click', '.sb-tab', function() {
                $('.sb-tab').removeClass('active');
                $(this).addClass('active');
                currentFilterTab = $(this).data('filter') || 'all';
                renderCatalog();
            });

            viewport.addEventListener('dragover', (ev) => {
                ev.preventDefault();
                ev.dataTransfer.dropEffect = 'copy';
                viewport.classList.add('dropok');
            });
            viewport.addEventListener('dragleave', () => viewport.classList.remove('dropok'));
            viewport.addEventListener('drop', (ev) => {
                ev.preventDefault();
                viewport.classList.remove('dropok');
                const subId = ev.dataTransfer.getData('text/plain');
                const n = byIdMap()[subId];
                if (!n || n.is_on_canvas) return;

                // Konversi titik drop ke world coordinates tepat di bawah kursor
                const pos = clientToWorld(ev.clientX, ev.clientY);
                n.x = Math.round(pos.x - 98);
                n.y = Math.round(pos.y - 37);
                n.is_on_canvas = true;

                createNodeEl(n);
                rebuildEdgeEls();
                redrawEdges();

                const cat = catalog.find(c => c.sub_menu_id === n.id);
                if (cat) cat.placed = true;
                renderCatalog();
                broadcastCanvasRefresh();

                $.post(`${BASE}/canvas/save-position`, {
                    _token: TOKEN,
                    nodes: [{
                        id: n.id,
                        x: Math.round(n.x),
                        y: Math.round(n.y)
                    }]
                });
                showToast(`Node "${n.name}" ditempatkan di kanvas`);
            });

            /* ============================================================
               Quick add sub-menu
               ============================================================ */
            document.getElementById('cvAddSubmenuBtn').onclick = () => {
                $('#cv_submenu_nama').val('');
                loadParentMenuOptions();
                new bootstrap.Modal(document.getElementById('modal_cv_add_submenu')).show();
            };

            function loadParentMenuOptions() {
                $.get(`${BASE}/menu-list`, function(res) {
                    const data = res.data || res || [];
                    const $s = $('#cv_submenu_parent_menu');
                    $s.empty().append('<option value="">Pilih Menu Induk...</option>');
                    data.forEach(m => $s.append(`<option value="${m.id}">${m.nama_menu}</option>`));
                }).fail(function() {
                    // Fallback: reuse the dependency menu-category endpoint which also lists ticket_menu
                    $.get(`${BASE}/dependency/menu-kategori`, function(res) {
                        const data = res.data || [];
                        const $s = $('#cv_submenu_parent_menu');
                        $s.empty().append('<option value="">Pilih Menu Induk...</option>');
                        data.forEach(m => $s.append(`<option value="${m.id}">${m.nama_menu}</option>`));
                    });
                });
            }
            $('#cv_btn_save_submenu').on('click', function() {
                const nama = $('#cv_submenu_nama').val().trim();
                const parentId = $('#cv_submenu_parent_menu').val();
                if (!nama || !parentId) {
                    Swal.fire('Perhatian', 'Nama sub-menu dan menu induk wajib diisi.', 'warning');
                    return;
                }
                const rect = viewport.getBoundingClientRect();
                const pos = clientToWorld(rect.left + rect.width / 2, rect.top + rect.height / 2);
                swalLoading();
                $.post(`${BASE}/canvas/submenu/quick-store`, {
                    _token: TOKEN,
                    nama_submenu: nama,
                    parent_menu_id: parentId,
                    x: Math.round(pos.x - 98),
                    y: Math.round(pos.y - 37)
                }, function(res) {
                    Swal.close();
                    $('#modal_cv_add_submenu').modal('hide');
                    if (res.code === 200) {
                        const d = res.data;
                        const n = {
                            id: d.id,
                            sub_menu_id: d.sub_menu_id,
                            name: d.name,
                            menu_name: d.menu_name,
                            x: d.x,
                            y: d.y,
                            w: 196,
                            has_org_tree: false,
                            flow_id: null,
                            is_on_canvas: true
                        };
                        nodes.push(n);
                        catalog.push({
                            sub_menu_id: n.id,
                            name: n.name,
                            menu_name: n.menu_name,
                            placed: true
                        });
                        createNodeEl(n);
                        renderCatalog(document.getElementById('cvCatalogSearch').value);
                        showToast(`Sub-menu "${n.name}" terdaftar ke master & kanvas`);
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                }).fail(function(e) {
                    Swal.close();
                    Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan', 'error');
                });
            });

            /* ============================================================
               Dependency modal (triggered by dragging a connector)
               ============================================================ */
            function openDependencyModal(fromId, toId, edge = null) {
                const from = byIdMap()[fromId],
                    to = byIdMap()[toId];
                $('#cv_dep_rule_id').val(edge ? edge.id : '');
                $('#cv_dep_source').val(fromId);
                $('#cv_dep_target').val(toId);
                $('#cv_dep_source_label').text(from ? `${from.name} (${from.kategori_name || 'Epicor'})` : '—');
                $('#cv_dep_target_label').text(to ? `${to.name} (${to.kategori_name || 'Epicor'})` : '—');
                $('#cv_modal_dep_title').text(edge ? 'Edit Dependency Rule' : 'Dependency Rule Baru');
                $('#cv_dep_description').val(edge ? (edge.description || '') : '');
                $('#cv_dep_is_bidirectional').prop('checked', edge ? (edge.bidirectional == 1 || edge.bidirectional === true) : false);
                $('#cv_dep_is_conditional').prop('checked', edge ? (edge.is_conditional == 1 || edge.is_conditional === true) : false);

                if (edge) {
                    $('#cv_dep_is_cross_env').prop('checked', edge.is_cross_environment == 1 || edge.is_cross_environment === true);
                } else {
                    // Auto-detect cross-environment on newly created connection
                    const autoCross = from && to && from.kategori && to.kategori && (from.kategori !== to.kategori);
                    $('#cv_dep_is_cross_env').prop('checked', !!autoCross);
                    if (autoCross && !$('#cv_dep_description').val()) {
                        $('#cv_dep_description').val(`Apakah perlu merevisi transaksi di ${to.kategori_name || 'Sistem Terkait'}?`);
                    }
                }

                new bootstrap.Modal(document.getElementById('modal_cv_dependency')).show();
            }

            $('#cv_btn_save_dependency').on('click', function() {
                const ruleId = $('#cv_dep_rule_id').val();
                const source = $('#cv_dep_source').val(),
                    target = $('#cv_dep_target').val();
                const isBidirectional     = $('#cv_dep_is_bidirectional').is(':checked') ? 1 : 0;
                const isConditional       = $('#cv_dep_is_conditional').is(':checked') ? 1 : 0;
                const isCrossEnvironment = $('#cv_dep_is_cross_env').is(':checked') ? 1 : 0;

                swalLoading();
                const url = ruleId ? `${BASE}/dependency/update/${ruleId}` : `${BASE}/dependency/store`;
                $.post(url, {
                    _token: TOKEN,
                    _method: ruleId ? 'PUT' : 'POST',
                    source_sub_menu_id: source,
                    target_sub_menu_id: target,
                    is_conditional: isConditional,
                    is_bidirectional: isBidirectional,
                    is_cross_environment: isCrossEnvironment,
                    description: $('#cv_dep_description').val(),
                }, function(res) {
                    Swal.close();
                    $('#modal_cv_dependency').modal('hide');
                    if (res.code === 200) {
                        loadCanvas();
                        showToast('Dependency rule tersimpan');
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }).fail(function(e) {
                    Swal.close();
                    Swal.fire('Error', e.responseJSON?.message, 'error');
                });
            });

            /* ============================================================
               Org Approval Tree modal (double-click a node)
               ============================================================ */
            let cvActiveFlowId = null,
                cvActiveSubMenuId = null,
                cvActiveStepId = null,
                cvCurrentSteps = [];

            function openOrgTree(node) {
                swalLoading();
                $.get(`${BASE}/canvas/org-tree/${node.sub_menu_id}`, function(res) {
                    Swal.close();
                    if (!res.has_flow) {
                        openChooseFlowModal(node, null, null);
                    } else {
                        cvActiveFlowId = res.flow_id;
                        cvActiveSubMenuId = node.sub_menu_id;
                        $('#cv_org_tree_title').text('Struktur Persetujuan — ' + node.name);
                        $('#cv_org_tree_subtitle').text('Flow: ' + res.flow_name);
                        new bootstrap.Modal(document.getElementById('modal_cv_org_tree')).show();
                        renderOrgTreeChart(res.steps, node);
                    }
                }).fail(function() {
                    Swal.close();
                    showToast('Gagal memuat org tree');
                });
            }

            /* ============================================================
               Choose / Reassign Approval Group modal
               Used both for the initial "no org tree yet" prompt and for the
               "Ganti Approval Group" button inside the org-tree modal, so a
               sub-menu can reuse another node's flow instead of being forced
               to set one up from scratch every time.
               ============================================================ */
            function openChooseFlowModal(node, currentFlowId, currentFlowName) {
                // Close large structure modal so it does not block the choose flow modal
                const orgTreeEl = document.getElementById('modal_cv_org_tree');
                if (orgTreeEl) {
                    const orgTreeModal = bootstrap.Modal.getInstance(orgTreeEl);
                    if (orgTreeModal) {
                        orgTreeModal.hide();
                    } else {
                        $('#modal_cv_org_tree').modal('hide');
                    }
                }

                $('#cv_cf_submenu_id').val(node.sub_menu_id);
                $('#cv_cf_submenu_label').text(node.name);
                $('#cv_cf_current_label').text(currentFlowName ? `Saat ini menggunakan: ${currentFlowName}` : 'Belum memiliki approval group.');
                $('#cv_choose_flow_title').text(currentFlowId ? 'Ganti Approval Group' : 'Atur Approval Group');
                $('#cv_cf_reset_wrap').toggle(!!currentFlowId);
                $('input[name="cv_cf_mode"][value="existing"]').prop('checked', true).trigger('change');
                $('#cv_flow_nama, #cv_flow_deskripsi').val('');

                $.get(`${BASE}/canvas/flow-list`, function(res) {
                    const $select = $('#cv_cf_existing_flow_id');
                    $select.empty().append('<option value="">Pilih approval group...</option>');
                    (res.data || []).forEach(f => {
                        if (f.id === currentFlowId) return; // don't offer re-selecting the same flow
                        $select.append(new Option(f.nama_flow, f.id));
                    });
                });

                const chooseFlowEl = document.getElementById('modal_cv_choose_flow');
                let chooseFlowModal = bootstrap.Modal.getInstance(chooseFlowEl);
                if (!chooseFlowModal) {
                    chooseFlowModal = new bootstrap.Modal(chooseFlowEl);
                }
                chooseFlowModal.show();
            }

            $('input[name="cv_cf_mode"]').on('change', function() {
                const mode = $('input[name="cv_cf_mode"]:checked').val();
                $('#cv_cf_panel_existing').toggleClass('d-none', mode !== 'existing');
                $('#cv_cf_panel_new').toggleClass('d-none', mode !== 'new');
            });

            $('#cv_btn_reassign_flow').on('click', function() {
                const node = byIdMap()[cvActiveSubMenuId];
                if (!node) return;
                openChooseFlowModal(node, cvActiveFlowId, node.flow_name);
            });

            $('#cv_btn_save_flow').on('click', function() {
                const subId = $('#cv_cf_submenu_id').val();
                const mode = $('input[name="cv_cf_mode"]:checked').val();
                const payload = {
                    _token: TOKEN,
                    sub_menu_id: subId,
                    mode: mode
                };

                if (mode === 'existing') {
                    const flowId = $('#cv_cf_existing_flow_id').val();
                    if (!flowId) {
                        Swal.fire('Perhatian', 'Pilih approval group terlebih dahulu.', 'warning');
                        return;
                    }
                    payload.flow_id = flowId;
                } else if (mode === 'new') {
                    const nama = $('#cv_flow_nama').val().trim();
                    if (!nama) {
                        Swal.fire('Perhatian', 'Nama flow wajib diisi.', 'warning');
                        return;
                    }
                    payload.nama_flow = nama;
                    payload.deskripsi = $('#cv_flow_deskripsi').val();
                }
                // mode === 'reset' needs no extra fields

                swalLoading();
                $.post(`${BASE}/canvas/submenu/set-flow`, payload, function(res) {
                    Swal.close();
                    $('#modal_cv_choose_flow').modal('hide');
                    if (res.code !== 200) {
                        Swal.fire('Gagal', res.message, 'error');
                        return;
                    }

                    const node = byIdMap()[subId];
                    if (!res.flow_id) {
                        // reset -> back to "Belum Ada Org Tree", close org tree modal if open
                        refreshNodeStatus(subId, false, null, null);
                        $('#modal_cv_org_tree').modal('hide');
                        showToast('Approval group direset ke default.');
                        if (canvasChannel) canvasChannel.whisper('canvas-refresh', {});
                        return;
                    }

                    cvActiveFlowId = res.flow_id;
                    cvActiveSubMenuId = subId;
                    $('#cv_org_tree_title').text('Struktur Persetujuan — ' + (node ? node.name : ''));
                    $('#cv_org_tree_subtitle').text('Flow: ' + res.flow_name);
                    new bootstrap.Modal(document.getElementById('modal_cv_org_tree')).show();
                    reloadOrgTree();
                    showToast(mode === 'new' ? 'Flow dibuat. Silakan tambahkan step pertama.' : 'Approval group diperbarui.');
                    if (canvasChannel) canvasChannel.whisper('canvas-refresh', {});
                }).fail(function(e) {
                    Swal.close();
                    Swal.fire('Error', e.responseJSON?.message, 'error');
                });
            });

            function buildOrgSubTree(steps) {
                if (!steps || steps.length === 0) return null;
                const map = {};
                steps.forEach(s => {
                    map[s.id] = Object.assign({}, s, {
                        children: []
                    });
                });
                const roots = [];
                steps.forEach(s => {
                    if (!s.parent_step_id) roots.push(map[s.id]);
                    else if (map[s.parent_step_id]) map[s.parent_step_id].children.push(map[s.id]);
                });
                if (roots.length === 1) return roots[0];
                if (roots.length > 1) return {
                    id: 'virtual-root',
                    step_name: '.',
                    children: roots
                };
                return null;
            }

            function renderOrgTreeChart(steps, node) {
                cvCurrentSteps = steps || [];
                const $container = $('#cv-org-tree-container');
                $container.empty();
                if (!steps || steps.length === 0) {
                    $container.html(`
                <div class="text-center py-5 text-muted">
                    <i class="mdi mdi-sitemap" style="font-size:3rem;opacity:.3"></i>
                    <p class="mt-2 mb-0">Belum ada step. Klik <strong>"Tambah Step Pertama"</strong> di atas.</p>
                </div>`);
                    return;
                }
                const treeData = buildOrgSubTree(steps);
                $container.orgchart({
                    data: treeData,
                    nodeContent: 'title',
                    pan: true,
                    zoom: true,
                    createNode: function($n, data) {
                        const roleClass = data.role_type || 'approver';
                        const isFinal = data.is_final ? 'final' : '';
                        const titleParts = [];
                        if (data.nama_departemen && data.nama_departemen !== 'null') titleParts.push(data.nama_departemen);
                        if (data.nama_divisi && data.nama_divisi !== 'null') titleParts.push(data.nama_divisi);
                        const empTitle = titleParts.length > 0 ? titleParts.join(' - ') : '—';
                        const roleBadgeMap = {
                            approver: '<span class="role-badge badge-approver"><i class="mdi mdi-check-circle" style="font-size:11px"></i> Approver</span>',
                            checker: '<span class="role-badge badge-checker"><i class="mdi mdi-eye" style="font-size:11px"></i> Checker</span>',
                            validator: '<span class="role-badge badge-validator"><i class="mdi mdi-shield-check" style="font-size:11px"></i> Validator</span>',
                        };
                        const finalBadge = data.is_final ? '<span class="role-badge badge-final ms-1"><i class="mdi mdi-flag-checkered" style="font-size:11px"></i> Final</span>' : '';
                        const html = `
                    <div class="modern-card ${roleClass} ${isFinal}" data-step-id="${data.id}" data-flow-id="${data.flow_id}">
                        <div class="card-header-gradient"></div>
                        <div class="card-body card-chart">
                            <div class="avatar-wrapper"><img src="{{ asset('admin/assets/img/avatars/1.png') }}" class="avatar-img"></div>
                            <div class="info-wrapper">
                                <div class="emp-name">${data.step_name || '—'}</div>
                                <div class="emp-title">${empTitle}</div>
                                <div>${roleBadgeMap[roleClass] || ''}${data.is_checker ? roleBadgeMap.checker : ''}${finalBadge}</div>
                            </div>
                        </div>
                        <div class="node-user-footer">
                            <i class="mdi mdi-account-group" style="font-size:13px"></i>
                            <span class="cv-user-count-val">${data.user_count ?? 0}</span> karyawan
                            ${(data.user_count ?? 0) > 0 ? '<span class="badge bg-success ms-1" style="font-size:9px">Assigned</span>' : '<span class="badge bg-warning text-dark ms-1" style="font-size:9px">Belum ada</span>'}
                        </div>
                        <div class="card-actions">
                            <button class="action-btn btn-add cv-btn-add-bawahan" title="Tambah Step Bawahan" data-step-id="${data.id}" data-step-name="${data.step_name}"><i class="mdi mdi-plus"></i></button>
                            <button class="action-btn btn-users cv-btn-manage-users" title="Kelola Karyawan" data-step-id="${data.id}" data-step-name="${data.step_name}"><i class="mdi mdi-account-multiple"></i></button>
                            <button class="action-btn btn-parent cv-btn-change-parent" title="Ganti Atasan / Parent Step"
                                data-step-id="${data.id}" data-step-name="${data.step_name||''}" data-parent-step-id="${data.parent_step_id||''}">
                                <i class="mdi mdi-sitemap"></i>
                            </button>
                            <button class="action-btn btn-edit cv-btn-edit-step" title="Edit Step"
                                data-step-id="${data.id}" data-step-name="${data.step_name||''}"
                                data-parent-step-id="${data.parent_step_id||''}"
                                data-holding-id="${data.holding_id||''}" data-departemen-id="${data.department_id||''}" data-jabatan="${data.jabatan_level_id||''}"
                                data-is-checker="${data.is_checker ? 1 : 0}">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button class="action-btn btn-delete cv-btn-delete-step" title="Hapus Step" data-step-id="${data.id}"><i class="mdi mdi-trash-can"></i></button>
                        </div>
                    </div>`;
                        $n.html(html);
                    }
                });
            }

            function reloadOrgTree() {
                $('#cvOrgTreeLoading').removeClass('d-none');
                $.get(`${BASE}/canvas/org-tree/${cvActiveSubMenuId}`, function(res) {
                    $('#cvOrgTreeLoading').addClass('d-none');
                    if (res.has_flow) {
                        renderOrgTreeChart(res.steps, byIdMap()[cvActiveSubMenuId]);
                        refreshNodeStatus(cvActiveSubMenuId, res.steps.length > 0, res.flow_id, res.flow_name);
                    }
                }).fail(() => $('#cvOrgTreeLoading').addClass('d-none'));
            }

            /* Select2 for Add / Edit Step modals & Change Parent modal */
            ['#cv_step_holding_id', '#cv_step_departemen_id', '#cv_step_jabatan_id'].forEach(sel => $(sel).select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#modal_cv_add_step')
            }));

            ['#cv_edit_step_holding_id', '#cv_edit_step_departemen_id', '#cv_edit_step_jabatan_id', '#cv_edit_step_parent_id'].forEach(sel => $(sel).select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#modal_cv_edit_step')
            }));

            $('#cv_cp_new_parent_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#modal_cv_change_parent')
            });

            /* Helper to prevent circular references in approval hierarchy */
            function getDescendantIds(steps, targetId) {
                const descendants = new Set();
                function addChildren(pid) {
                    steps.forEach(s => {
                        if (String(s.parent_step_id) === String(pid) && !descendants.has(s.id)) {
                            descendants.add(s.id);
                            addChildren(s.id);
                        }
                    });
                }
                addChildren(targetId);
                return descendants;
            }

            function populateParentOptions(selectId, currentStepId, currentParentId) {
                const $select = $(selectId);
                $select.empty().append('<option value="">-- Sebagai Step Pertama (Root / Tidak Ada Atasan) --</option>');
                const excluded = getDescendantIds(cvCurrentSteps, currentStepId);
                excluded.add(currentStepId);

                cvCurrentSteps.forEach(s => {
                    if (!excluded.has(s.id)) {
                        const deptName = s.nama_departemen ? ` (${s.nama_departemen})` : '';
                        const isSelected = String(s.id) === String(currentParentId);
                        $select.append(new Option(`${s.step_name}${deptName}`, s.id, false, isSelected));
                    }
                });
                $select.val(currentParentId || '').trigger('change');
            }

            /* Add root step */
            $('#cv_btn_add_root_step').on('click', function() {
                openAddStepModal(cvActiveFlowId, null, null);
            });
            /* Add child step */
            $(document).on('click', '.cv-btn-add-bawahan', function(e) {
                e.stopPropagation();
                openAddStepModal(cvActiveFlowId, $(this).data('step-id'), $(this).data('step-name'));
            });

            function openAddStepModal(flowId, parentStepId, parentStepName) {
                $('#cv_step_flow_id').val(flowId);
                $('#cv_step_parent_id').val(parentStepId || '');
                $('#cv_step_id_edit, #cv_step_nama').val('');
                $('#cv_step_is_checker').prop('checked', false);
                $('#cv_step_holding_id').val(null).trigger('change');
                $('#cv_step_departemen_id').empty().append('<option value="">Pilih Departemen...</option>').trigger('change');
                loadJabatanOptions('#cv_step_jabatan_id');
                if (parentStepId) {
                    $('#cv_modal_step_title').text('Tambah Step Bawahan');
                    $('#cv_parent_step_info').removeClass('d-none');
                    $('#cv_parent_step_name_info').text(parentStepName);
                } else {
                    $('#cv_modal_step_title').text('Tambah Step Pertama');
                    $('#cv_parent_step_info').addClass('d-none');
                }
                loadHoldingOptions('#cv_step_holding_id');
                new bootstrap.Modal(document.getElementById('modal_cv_add_step')).show();
            }

            $('#cv_step_holding_id').on('change', function() {
                if ($(this).data('silent-change')) return;
                loadDepartemenOptions('#cv_step_departemen_id', $(this).val());
            });

            $('#cv_btn_save_step').on('click', function() {
                const flowId = $('#cv_step_flow_id').val();
                const parentId = $('#cv_step_parent_id').val();
                const nama = $('#cv_step_nama').val().trim();
                const jabatan = $('#cv_step_jabatan_id').val();
                const dept = $('#cv_step_departemen_id').val();
                if (!nama) {
                    Swal.fire('Perhatian', 'Nama step wajib diisi.', 'warning');
                    return;
                }
                if (!jabatan || !dept) {
                    Swal.fire('Perhatian', 'Departemen dan level jabatan wajib diisi.', 'warning');
                    return;
                }
                swalLoading();
                $.post(`${BASE}/approval-flow/step/store`, {
                    _token: TOKEN,
                    flow_id: flowId,
                    parent_step_id: parentId || null,
                    nama_step: nama,
                    holding_id: $('#cv_step_holding_id').val(),
                    departemen_id: dept,
                    jabatan_id: jabatan,
                    is_checker: $('#cv_step_is_checker').is(':checked') ? 1 : 0,
                }, function(res) {
                    Swal.close();
                    $('#modal_cv_add_step').modal('hide');
                    if (res.code === 200) {
                        reloadOrgTree();
                        showToast('Step tersimpan');
                    } else Swal.fire('Gagal', res.message, 'error');
                }).fail(function(e) {
                    Swal.close();
                    Swal.fire('Error', e.responseJSON?.message, 'error');
                });
            });

            /* Quick Change Parent */
            $(document).on('click', '.cv-btn-change-parent', function(e) {
                e.stopPropagation();
                const $btn = $(this);
                const stepId = $btn.data('step-id');
                const stepName = $btn.data('step-name') || '';
                const parentStepId = String($btn.data('parent-step-id') || '');

                $('#cv_cp_step_id').val(stepId);
                $('#cv_cp_step_name').val(stepName);
                const subTitle = $('#cv_org_tree_subtitle').text().replace('Flow: ', '') || '';
                $('#cv_change_parent_subtitle').text(subTitle ? 'Flow: ' + subTitle : '');

                populateParentOptions('#cv_cp_new_parent_id', stepId, parentStepId);

                new bootstrap.Modal(document.getElementById('modal_cv_change_parent')).show();
            });

            $('#cv_btn_save_change_parent').on('click', function() {
                const stepId = $('#cv_cp_step_id').val();
                const newParentId = $('#cv_cp_new_parent_id').val();
                if (!stepId) return;

                swalLoading();
                $.ajax({
                    url: `${BASE}/approval-flow/step/change-parent/${stepId}`,
                    type: 'POST',
                    data: {
                        _token: TOKEN,
                        flow_id: cvActiveFlowId,
                        parent_step_id: newParentId || null,
                    },
                    success: function(res) {
                        Swal.close();
                        $('#modal_cv_change_parent').modal('hide');
                        if (res.code === 200) {
                            reloadOrgTree();
                            showToast(res.message || 'Struktur atasan berhasil diubah');
                            if (typeof canvasChannel !== 'undefined' && canvasChannel) canvasChannel.whisper('canvas-refresh', {});
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function(e) {
                        Swal.close();
                        Swal.fire('Error', e.responseJSON?.message || 'Gagal memindahkan parent step.', 'error');
                    }
                });
            });

            /* Edit step */
            $(document).on('click', '.cv-btn-edit-step', function(e) {
                e.stopPropagation();
                const $btn = $(this);
                const v = {
                    stepId: $btn.data('step-id'),
                    stepName: $btn.data('step-name') || '',
                    parentStepId: String($btn.data('parent-step-id') || ''),
                    holdingId: String($btn.data('holding-id') || ''),
                    departemenId: String($btn.data('departemen-id') || ''),
                    jabatanId: String($btn.data('jabatan') || ''),
                    isChecker: Boolean($btn.data('is-checker')),
                };
                $('#cv_edit_step_id').val(v.stepId);
                $('#cv_edit_step_flow_id').val(cvActiveFlowId);
                $('#cv_edit_step_nama').val(v.stepName);
                $('#cv_edit_step_is_checker').prop('checked', v.isChecker);

                // Populate parent options in edit step modal
                populateParentOptions('#cv_edit_step_parent_id', v.stepId, v.parentStepId);

                $('#cv_edit_step_departemen_id').empty().append('<option value="">Pilih Departemen...</option>').trigger('change');
                $('#cv_edit_step_jabatan_id').empty().append('<option value="">Pilih Level Jabatan...</option>').trigger('change');
                new bootstrap.Modal(document.getElementById('modal_cv_edit_step')).show();

                loadHoldingOptions('#cv_edit_step_holding_id', v.holdingId, function() {
                    loadDepartemenOptions('#cv_edit_step_departemen_id', v.holdingId, v.departemenId, function() {
                        loadJabatanOptions('#cv_edit_step_jabatan_id', v.jabatanId);
                    });
                });
            });
            $('#cv_edit_step_holding_id').on('change', function() {
                if ($(this).data('silent-change')) return;
                loadDepartemenOptions('#cv_edit_step_departemen_id', $(this).val());
            });

            $('#cv_btn_update_step').on('click', function() {
                const id = $('#cv_edit_step_id').val();
                const nama = $('#cv_edit_step_nama').val().trim();
                const parentId = $('#cv_edit_step_parent_id').val();
                const deptId = $('#cv_edit_step_departemen_id').val();
                const jabatanId = $('#cv_edit_step_jabatan_id').val();
                const isChecker = $('#cv_edit_step_is_checker').is(':checked') ? 1 : 0;
                if (!nama) {
                    Swal.fire('Perhatian', 'Nama step wajib diisi.', 'warning');
                    return;
                }
                if (!deptId || !jabatanId) {
                    Swal.fire('Perhatian', 'Departemen dan level jabatan wajib diisi.', 'warning');
                    return;
                }
                swalLoading();
                $.ajax({
                    url: `${BASE}/approval-flow/step/update/${id}`,
                    type: 'POST',
                    data: {
                        _token: TOKEN,
                        _method: 'POST',
                        flow_id: cvActiveFlowId,
                        nama_step: nama,
                        parent_step_id: parentId || null,
                        holding_id: $('#cv_edit_step_holding_id').val(),
                        departemen_id: deptId,
                        jabatan_id: jabatanId,
                        is_checker: isChecker,
                    },
                    success: function(res) {
                        Swal.close();
                        $('#modal_cv_edit_step').modal('hide');
                        if (res.code === 200) {
                            reloadOrgTree();
                            showToast('Step diperbarui');
                            if (typeof canvasChannel !== 'undefined' && canvasChannel) canvasChannel.whisper('canvas-refresh', {});
                        } else Swal.fire('Gagal', res.message, 'error');
                    },
                    error: function(e) {
                        Swal.close();
                        Swal.fire('Error', e.responseJSON?.message, 'error');
                    }
                });
            });

            /* Delete step */
            $(document).on('click', '.cv-btn-delete-step', function(e) {
                e.stopPropagation();
                const stepId = $(this).data('step-id');
                Swal.fire({
                    title: 'Hapus Step?',
                    text: 'Step bawahan juga akan terhapus secara cascade.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ea5455'
                }).then(r => {
                    if (!r.isConfirmed) return;
                    swalLoading();
                    $.ajax({
                        url: `${BASE}/approval-flow/step/delete/${stepId}`,
                        type: 'POST',
                        data: {
                            _token: TOKEN,
                            _method: 'DELETE'
                        },
                        success: function(res) {
                            Swal.close();
                            reloadOrgTree();
                            showToast(res.message || 'Step dihapus');
                        },
                        error: function(e) {
                            Swal.close();
                            Swal.fire('Error', e.responseJSON?.message, 'error');
                        }
                    });
                });
            });

            /* Manage karyawan */
            let cvAllKaryawan = [],
                cvCheckedSet = new Set();
            $(document).on('click', '.cv-btn-manage-users', function(e) {
                e.stopPropagation();
                cvActiveStepId = $(this).data('step-id');
                $('#cv_modal_karyawan_label').text(`Step ${$(this).data('step-name')}`);
                $('#cv_search_karyawan').val('');
                new bootstrap.Modal(document.getElementById('modal_cv_pilih_karyawan')).show();
                loadKaryawanCanvas(cvActiveStepId);
            });

            function loadKaryawanCanvas(stepId) {
                $('#cv-karyawan-list').html('<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Memuat data...</div>');
                cvCheckedSet.clear();
                updateSelectedCountCanvas();
                $.ajax({
                    url: `${BASE}/approval-flow/karyawan/${stepId}`,
                    type: 'GET',
                    success: function(res) {
                        cvAllKaryawan = res.data || [];
                        cvAllKaryawan.forEach(k => {
                            if (k.checked) cvCheckedSet.add(String(k.id));
                        });
                        renderKaryawanCanvas(cvAllKaryawan);
                    },
                    error: function() {
                        $('#cv-karyawan-list').html('<div class="text-center py-5 text-danger">Gagal memuat data karyawan.</div>');
                    }
                });
            }

            function renderKaryawanCanvas(list) {
                if (!list.length) {
                    $('#cv-karyawan-list').html('<div class="text-center py-5 text-muted">Tidak ada karyawan yang tersedia.</div>');
                    syncCheckAllCanvas();
                    return;
                }
                const html = list.map(k => {
                    const sid = String(k.id);
                    const isChecked = cvCheckedSet.has(sid);
                    const initial = (k.nama || '?').charAt(0).toUpperCase();
                    return `<label class="karyawan-item d-flex align-items-center gap-3 px-3 py-2 m-0 w-100 ${isChecked?'selected':''}">
                <input type="checkbox" class="form-check-input flex-shrink-0 mt-0" name="cv_karyawan_ids[]" value="${sid}" ${isChecked?'checked':''}>
                <div class="karyawan-avatar">${initial}</div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-semibold text-truncate" style="font-size:13px">${k.nama}</div>
                    <div class="text-muted text-truncate" style="font-size:11px">${k.divisi} &middot; ${k.bagian} &middot; ${k.jabatan}</div>
                </div>
                <i class="mdi mdi-check-circle text-primary check-icon flex-shrink-0" style="font-size:18px;opacity:${isChecked?1:0}"></i>
            </label>`;
                }).join('');
                $('#cv-karyawan-list').html(html);
                syncCheckAllCanvas();
                updateSelectedCountCanvas();
            }
            $(document).on('input', '#cv_search_karyawan', function() {
                const q = $(this).val().toLowerCase().trim();
                const filtered = q ? cvAllKaryawan.filter(k => k.nama.toLowerCase().includes(q) || k.divisi.toLowerCase().includes(q) || k.bagian.toLowerCase().includes(q)) : cvAllKaryawan;
                renderKaryawanCanvas(filtered);
            });
            $(document).on('change', '#cv-karyawan-list input[name="cv_karyawan_ids[]"]', function() {
                const sid = String($(this).val());
                const checked = $(this).is(':checked');
                const $item = $(this).closest('.karyawan-item');
                if (checked) cvCheckedSet.add(sid);
                else cvCheckedSet.delete(sid);
                $item.toggleClass('selected', checked);
                $item.find('.check-icon').css('opacity', checked ? 1 : 0);
                syncCheckAllCanvas();
                updateSelectedCountCanvas();
            });
            $(document).on('change', '#cv_check_all_karyawan', function() {
                const checked = $(this).is(':checked');
                $('#cv-karyawan-list input[name="cv_karyawan_ids[]"]').each(function() {
                    const sid = String($(this).val());
                    $(this).prop('checked', checked);
                    $(this).closest('.karyawan-item').toggleClass('selected', checked).find('.check-icon').css('opacity', checked ? 1 : 0);
                    if (checked) cvCheckedSet.add(sid);
                    else cvCheckedSet.delete(sid);
                });
                updateSelectedCountCanvas();
            });

            function syncCheckAllCanvas() {
                const $visible = $('#cv-karyawan-list input[name="cv_karyawan_ids[]"]');
                const total = $visible.length,
                    checkedN = $visible.filter(':checked').length;
                $('#cv_check_all_karyawan').prop('checked', total > 0 && checkedN === total).prop('indeterminate', checkedN > 0 && checkedN < total);
            }

            function updateSelectedCountCanvas() {
                const count = cvCheckedSet.size;
                $('#cv-selected-count').toggleClass('d-none', count === 0).text(`${count} Terpilih`);
            }
            $('#cv_btn_save_karyawan').on('click', function() {
                const $btn = $(this);
                const ids = [...cvCheckedSet];
                $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Menyimpan...');
                $.ajax({
                    url: `${BASE}/approval-flow/save-user`,
                    type: 'POST',
                    data: {
                        _token: TOKEN,
                        step_id: cvActiveStepId,
                        karyawan_ids: ids
                    },
                    success: function(res) {
                        $('#modal_cv_pilih_karyawan').modal('hide');
                        if (res.code === 200) {
                            reloadOrgTree();
                            showToast(res.message || 'Karyawan tersimpan');
                        } else Swal.fire('Gagal', res.message, 'error');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i>Simpan');
                    }
                });
            });

            /* ============================================================
               Shared cascade dropdown loaders (scoped to this IIFE — does not
               collide with the same-named functions used by Validator/Assignment
               tabs elsewhere on the page)
               ============================================================ */
            function loadHoldingOptions(selectId, selectedId = null, callback = null) {
                $.ajax({
                    url: `${BASE}/approval-flow/get-holding`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        const $select = $(selectId);
                        $select.empty().append('<option value="">Pilih Holding...</option>');
                        data.forEach(item => $select.append(new Option(item.text, item.id, false, String(item.id) === String(selectedId))));
                        $select.data('silent-change', true);
                        if (selectedId) $select.val(selectedId).trigger('change');
                        else $select.val('').trigger('change');
                        $select.data('silent-change', false);
                        if (typeof callback === 'function') callback();
                    },
                    error: function() {
                        if (typeof callback === 'function') callback();
                    }
                });
            }

            function loadDepartemenOptions(selectId, holdingId, selectedId = null, callback = null) {
                const $select = $(selectId);
                $select.empty().append('<option value="">Pilih Departemen...</option>');
                if (!holdingId) {
                    $select.data('silent-change', true);
                    $select.val('').trigger('change');
                    $select.data('silent-change', false);
                    if (typeof callback === 'function') callback();
                    return;
                }
                $.ajax({
                    url: `${BASE}/approval-flow/get-departemen`,
                    type: 'GET',
                    data: {
                        holding: holdingId
                    },
                    dataType: 'json',
                    success: function(data) {
                        data.forEach(item => $select.append(new Option(item.text, item.id, false, String(item.id) === String(selectedId))));
                        $select.data('silent-change', true);
                        if (selectedId) $select.val(selectedId).trigger('change');
                        else $select.val('').trigger('change');
                        $select.data('silent-change', false);
                        if (typeof callback === 'function') callback();
                    },
                    error: function() {
                        if (typeof callback === 'function') callback();
                    }
                });
            }

            function loadJabatanOptions(selectId, selectedId = null, callback = null) {
                const $select = $(selectId);
                $select.empty().append('<option value="">Pilih Level Jabatan...</option>');
                $.ajax({
                    url: `${BASE}/approval-flow/get-jabatan-level`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        data.forEach(item => $select.append(new Option(item.text, item.id, false, String(item.id) === String(selectedId))));
                        if (selectedId) $select.val(selectedId).trigger('change');
                        else $select.trigger('change');
                        if (typeof callback === 'function') callback();
                    },
                    error: function() {
                        if (typeof callback === 'function') callback();
                    }
                });
            }

            /* ============================================================
               Real-time Multiplayer Collaboration (Laravel Reverb & Echo)
               ============================================================ */
            @php
                $authUser = auth()->user();
                $authKaryawanName = null;
                if ($authUser && !empty($authUser->karyawan_id)) {
                    $authKaryawanName = \Illuminate\Support\Facades\DB::table('karyawans')
                        ->where('nomor_identitas_karyawan', $authUser->karyawan_id)
                        ->orWhere('nik', $authUser->karyawan_id)
                        ->orWhere('id', $authUser->karyawan_id)
                        ->value('name');
                }
                $authDisplayName = $authKaryawanName ?: ($authUser->name ?? ($authUser->username ?? 'User'));
            @endphp
            const currentUserId = {{ (int) (auth()->id() ?? 1) }};
            const currentUserName = @json($authDisplayName);
            const userColorPalette = ['#7c3aed', '#2563eb', '#059669', '#d97706', '#dc2626', '#db2777', '#4f46e5', '#0891b2', '#ea580c'];
            const myColor = userColorPalette[currentUserId % userColorPalette.length] || '#7c3aed';

            let echoInstance = null;
            let canvasChannel = null;
            const activeCursors = {};
            const activeCollaborators = {};

            function initMultiplayer() {
                if (typeof Echo === 'undefined') {
                    console.warn('Laravel Echo library not available');
                    return;
                }
                
                const broadcaster = @json(config('broadcasting.default') ?: env('BROADCAST_CONNECTION', 'pusher'));
                const pusherKey = @json(config('broadcasting.connections.pusher.key') ?: env('PUSHER_APP_KEY'));
                const pusherCluster = @json(config('broadcasting.connections.pusher.options.cluster') ?: env('PUSHER_APP_CLUSTER', 'ap1'));
                
                const reverbKey = @json(config('broadcasting.connections.reverb.key') ?: env('REVERB_APP_KEY'));
                let reverbHost = @json(config('broadcasting.connections.reverb.options.host') ?: env('REVERB_HOST', 'localhost')) || window.location.hostname;
                reverbHost = String(reverbHost).replace(/^https?:\/\//i, '').replace(/\/+$/, '');
                const reverbPort = {{ (int) (config('broadcasting.connections.reverb.options.port') ?: env('REVERB_PORT', 8080)) }};
                const reverbScheme = @json(config('broadcasting.connections.reverb.options.scheme') ?: env('REVERB_SCHEME', 'https'));
                const isSecure = (reverbScheme === 'https') || (window.location.protocol === 'https:');

                try {
                    if (broadcaster === 'pusher' && (pusherKey || reverbKey)) {
                        const activeKey = pusherKey || reverbKey;
                        echoInstance = new Echo({
                            broadcaster: 'pusher',
                            key: activeKey,
                            cluster: pusherCluster || 'ap1',
                            forceTLS: true,
                            authEndpoint: '{{ url("/broadcasting/auth") }}',
                            auth: {
                                headers: {
                                    'X-CSRF-TOKEN': TOKEN
                                }
                            }
                        });
                    } else if (reverbKey) {
                        echoInstance = new Echo({
                            broadcaster: 'reverb',
                            key: reverbKey,
                            wsHost: reverbHost,
                            wsPort: reverbPort,
                            wssPort: reverbPort,
                            forceTLS: isSecure,
                            enabledTransports: ['ws', 'wss'],
                            authEndpoint: '{{ url("/broadcasting/auth") }}',
                            auth: {
                                headers: {
                                    'X-CSRF-TOKEN': TOKEN
                                }
                            }
                        });
                    } else {
                        console.warn('Neither Pusher nor Reverb credentials configured.');
                        return;
                    }

                    canvasChannel = echoInstance.join('canvas.workflow');

                    // Presence: user join & leave
                    canvasChannel.here((users) => {
                        users.forEach(u => activeCollaborators[u.id] = u);
                        renderCollabAvatars();
                    });

                    canvasChannel.joining((user) => {
                        activeCollaborators[user.id] = user;
                        renderCollabAvatars();
                        showToast(`${user.name} bergabung ke kanvas`);
                    });

                    canvasChannel.leaving((user) => {
                        delete activeCollaborators[user.id];
                        removeRemoteCursor(user.id);
                        renderCollabAvatars();
                    });

                    // Live cursor whisper
                    canvasChannel.listenForWhisper('cursor-move', (data) => {
                        if (data && data.id !== currentUserId) {
                            renderRemoteCursor(data);
                        }
                    });

                    // Live node drag whisper
                    canvasChannel.listenForWhisper('node-drag', (data) => {
                        if (data && data.id) {
                            const n = byIdMap()[data.id];
                            if (n) {
                                n.x = data.x;
                                n.y = data.y;
                                const el = nodeEls[data.id];
                                if (el) {
                                    el.style.left = data.x + 'px';
                                    el.style.top = data.y + 'px';
                                    el.classList.add('remote-active');
                                    el.style.setProperty('--remote-user-color', data.color || '#ec4899');
                                    let badge = el.querySelector('.remote-user-badge');
                                    if (!badge) {
                                        badge = document.createElement('div');
                                        badge.className = 'remote-user-badge';
                                        el.appendChild(badge);
                                    }
                                    badge.style.background = data.color || '#ec4899';
                                    badge.textContent = data.name ? `${data.name} menggeser...` : 'Editing...';
                                }
                                redrawEdges();
                            }
                        }
                    });

                    canvasChannel.listenForWhisper('node-drag-end', (data) => {
                        if (data && data.id) {
                            const el = nodeEls[data.id];
                            if (el) {
                                el.classList.remove('remote-active');
                                const badge = el.querySelector('.remote-user-badge');
                                if (badge) badge.remove();
                            }
                        }
                    });

                    // Live canvas changes (place / unplace / save dependency)
                    canvasChannel.listenForWhisper('canvas-refresh', () => {
                        loadCanvas();
                    });

                } catch (err) {
                    console.error('Failed to initialize Reverb:', err);
                }
            }

            function renderCollabAvatars() {
                const list = Object.values(activeCollaborators);
                const $container = $('#cvCollabAvatars');
                $container.empty();

                list.slice(0, 4).forEach(u => {
                    const initial = (u.name || 'U').charAt(0).toUpperCase();
                    const color = u.color || '#7c3aed';
                    $container.append(`<div class="collab-avatar" style="background:${color}" title="${u.name}">${initial}</div>`);
                });

                if (list.length > 4) {
                    $container.append(`<div class="collab-avatar" style="background:#4b5563">+${list.length - 4}</div>`);
                }

                $('#cvCollabCount').text(`${Math.max(1, list.length)} Online`);
            }

            function renderRemoteCursor(data) {
                let el = activeCursors[data.id];
                if (!el) {
                    el = document.createElement('div');
                    el.className = 'cv-remote-cursor';
                    el.id = `remote-cursor-${data.id}`;
                    el.innerHTML = `
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="${data.color || '#7c3aed'}">
                            <path d="M3 3l7 18 3-7 7-3L3 3z"/>
                        </svg>
                        <span class="cursor-label" style="background:${data.color || '#7c3aed'}">${data.name}</span>
                    `;
                    world.appendChild(el);
                    activeCursors[data.id] = el;
                }

                el.style.transform = `translate(${data.x}px, ${data.y}px)`;
                el.style.opacity = '1';

                clearTimeout(el._idleTimer);
                el._idleTimer = setTimeout(() => {
                    el.style.opacity = '0';
                }, 3000);
            }

            function removeRemoteCursor(id) {
                if (activeCursors[id]) {
                    activeCursors[id].remove();
                    delete activeCursors[id];
                }
            }

            // Throttled mouse cursor whisper broadcaster (~28 FPS)
            let lastCursorSend = 0;
            let lastCursorPos = { x: 0, y: 0 };
            viewport.addEventListener('mousemove', (ev) => {
                if (!canvasChannel) return;
                const now = Date.now();
                if (now - lastCursorSend < 35) return;

                const pos = clientToWorld(ev.clientX, ev.clientY);
                const dx = Math.abs(pos.x - lastCursorPos.x);
                const dy = Math.abs(pos.y - lastCursorPos.y);
                if (dx < 3 && dy < 3) return;

                lastCursorSend = now;
                lastCursorPos = pos;

                canvasChannel.whisper('cursor-move', {
                    id: currentUserId,
                    name: currentUserName,
                    color: myColor,
                    x: Math.round(pos.x),
                    y: Math.round(pos.y)
                });
            });

            function broadcastNodeDrag(nodeId, x, y) {
                if (canvasChannel) {
                    canvasChannel.whisper('node-drag', {
                        id: nodeId,
                        x: x,
                        y: y,
                        name: currentUserName,
                        color: myColor
                    });
                }
            }

            function broadcastNodeDragEnd(nodeId) {
                if (canvasChannel) {
                    canvasChannel.whisper('node-drag-end', { id: nodeId });
                }
            }

            function broadcastCanvasRefresh() {
                if (canvasChannel) {
                    canvasChannel.whisper('canvas-refresh', {});
                }
            }

            /* ============================================================
               INIT
               ============================================================ */
            loadCanvas();
            initMultiplayer();

        })(jQuery);
    });
</script>