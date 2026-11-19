(function () {
    'use strict';

    const pageSelector = '.master-table-page';
    const areaSelector = '.master-table-area';
    let resizeFrame = null;
    let lastPagingEventAt = 0;

    function syncProcessingState(area) {
        const processing = area.querySelector('.dt-processing');
        const isProcessing = Boolean(
            processing &&
            window.getComputedStyle(processing).display !== 'none' &&
            window.getComputedStyle(processing).visibility !== 'hidden'
        );

        area.classList.toggle('is-processing', isProcessing);
    }

    function resizeArea(area) {
        syncProcessingState(area);

        const scrollBody = area.querySelector('.dt-scroll-body');

        if (!scrollBody) {
            return;
        }

        const tableRow =
            scrollBody.closest('.dt-layout-table') ||
            scrollBody.closest('.dt-layout-row');
        const container = scrollBody.closest('.dt-container');
        const paging = container ? container.querySelector('.dt-paging') : null;
        const info = container ? container.querySelector('.dt-info') : null;
        const footerRow = paging
            ? paging.closest('.dt-layout-row, .row')
            : info
                ? info.closest('.dt-layout-row, .row')
                : null;
        let footerHeight = 0;

        if (tableRow) {
            let sibling = tableRow.nextElementSibling;

            while (sibling) {
                if (sibling.offsetParent !== null) {
                    const siblingStyle = window.getComputedStyle(sibling);
                    footerHeight +=
                        sibling.getBoundingClientRect().height +
                        (parseFloat(siblingStyle.marginTop) || 0) +
                        (parseFloat(siblingStyle.marginBottom) || 0);
                }
                sibling = sibling.nextElementSibling;
            }
        }

        if (footerRow) {
            const footerStyle = window.getComputedStyle(footerRow);
            const measuredFooterHeight =
                footerRow.getBoundingClientRect().height +
                (parseFloat(footerStyle.marginTop) || 0) +
                (parseFloat(footerStyle.marginBottom) || 0);

            footerHeight = Math.max(footerHeight, measuredFooterHeight, 48);
        }

        const availableHeight = Math.max(
            120,
            area.getBoundingClientRect().bottom -
                scrollBody.getBoundingClientRect().top -
                footerHeight -
                8
        );

        scrollBody.style.height = `${availableHeight}px`;
        scrollBody.style.maxHeight = `${availableHeight}px`;
    }

    function resizeAll() {
        bindDataTableEvents();
        syncReportToolbars();
        adjustDataTables();
        document.querySelectorAll(areaSelector).forEach(resizeArea);
    }

    function adjustDataTables() {
        if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.dataTable) {
            return;
        }

        const tables = window.jQuery.fn.dataTable.tables({ visible: true, api: true });

        tables.columns.adjust();

        if (tables.responsive && typeof tables.responsive.recalc === 'function') {
            tables.responsive.recalc();
        }
    }

    function syncReportToolbars() {
        document.querySelectorAll('.master-report-toolbar').forEach(function (toolbar) {
            const card = toolbar.closest('.master-table-card');
            const row = toolbar.classList.contains('d-flex')
                ? toolbar
                : toolbar.querySelector(':scope > .d-flex');

            if (!card || !row) {
                return;
            }

            card.classList.add('master-report-card');
            row.classList.add('master-report-toolbar-row');

            let controls = row.querySelector(':scope > .master-report-controls');

            if (!controls) {
                controls = document.createElement('div');
                controls.className = 'master-report-controls';
                controls.innerHTML =
                    '<div class="master-report-export"></div>' +
                    '<div class="master-report-length"></div>' +
                    '<div class="master-report-search"></div>';
                row.appendChild(controls);
            }

            moveReportControls(
                card,
                '.master-table-toolbar .dt-buttons, .master-table-area .dt-buttons',
                controls.querySelector('.master-report-export')
            );
            moveReportControls(
                card,
                '.master-table-area .dt-length',
                controls.querySelector('.master-report-length')
            );
            moveReportControls(
                card,
                '.master-table-area .dt-search',
                controls.querySelector('.master-report-search')
            );

            syncReportControlVisibility(card, controls);
        });
    }

    function bindDataTableEvents() {
        if (!window.jQuery) {
            return;
        }

        document.querySelectorAll(`${areaSelector} table`).forEach(function (table) {
            if (table.dataset.masterTableLayoutBound === 'true') {
                return;
            }

            table.dataset.masterTableLayoutBound = 'true';

            window.jQuery(table)
                .on('processing.dt.masterTableLayout', function (_event, _settings, processing) {
                    const area = table.closest(areaSelector);

                    if (!area) {
                        return;
                    }

                    area.classList.toggle('is-processing', Boolean(processing));

                    if (!processing) {
                        scheduleResize();
                    }
                })
                .on(
                    'draw.dt.masterTableLayout xhr.dt.masterTableLayout page.dt.masterTableLayout length.dt.masterTableLayout search.dt.masterTableLayout order.dt.masterTableLayout',
                    function () {
                        const area = table.closest(areaSelector);

                        if (area) {
                            area.classList.remove('is-processing');
                        }

                        window.setTimeout(scheduleResize, 0);
                    }
                );
        });
    }

    function getDataTableFromPaging(control) {
        if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.DataTable) {
            return null;
        }

        const area = control.closest(areaSelector);
        const container = control.closest('.dt-container, .dataTables_wrapper');

        if (!area) {
            return null;
        }

        const isDataTable =
            (window.DataTable && window.DataTable.isDataTable) ||
            window.jQuery.fn.DataTable.isDataTable ||
            (window.jQuery.fn.dataTable && window.jQuery.fn.dataTable.isDataTable);

        if (!isDataTable) {
            return null;
        }

        const candidates = [
            container?.querySelector('.dt-scroll-body table[id], .dataTables_scrollBody table[id]'),
            container?.querySelector('table[id]'),
            area.querySelector('.dt-scroll-body table[id], .dataTables_scrollBody table[id]'),
            area.querySelector('table[id]'),
        ].filter(Boolean);

        for (const table of candidates) {
            if (isDataTable(table)) {
                const dataTable = window.jQuery(table).DataTable();

                if (getPageInfo(dataTable)) {
                    return dataTable;
                }
            }
        }

        if (window.jQuery.fn.dataTable && typeof window.jQuery.fn.dataTable.tables === 'function') {
            const tables = window.jQuery.fn.dataTable.tables();

            for (const table of tables) {
                if (area.contains(table)) {
                    const dataTable = window.jQuery(table).DataTable();

                    if (getPageInfo(dataTable)) {
                        return dataTable;
                    }
                }
            }
        }

        return null;
    }

    function getPageInfo(table) {
        if (!table || !table.page || typeof table.page.info !== 'function') {
            return null;
        }

        const info = table.page.info();

        if (!info || typeof info.page !== 'number' || typeof info.pages !== 'number') {
            return null;
        }

        return info;
    }

    function resolvePagingTarget(info, control) {
        const item = control.closest('.page-item, .dt-paging-button, .paginate_button');
        const itemClass = item ? item.className : '';
        const controlClass = control.className || '';
        const dtIndex = control.dataset.dtIdx || item?.dataset.dtIdx || '';
        const label = (control.getAttribute('aria-label') || control.textContent || '').trim().toLowerCase();

        if (itemClass.includes('first') || controlClass.includes('first') || dtIndex === 'first' || label === 'first') {
            return 0;
        }

        if (
            itemClass.includes('previous') ||
            controlClass.includes('previous') ||
            dtIndex === 'previous' ||
            label === 'previous' ||
            label === '<' ||
            label === '\u2039' ||
            label === '\u00ab'
        ) {
            return Math.max(info.page - 1, 0);
        }

        if (
            itemClass.includes('next') ||
            controlClass.includes('next') ||
            dtIndex === 'next' ||
            label === 'next' ||
            label === '>' ||
            label === '\u203a' ||
            label === '\u00bb'
        ) {
            return Math.min(info.page + 1, info.pages - 1);
        }

        if (itemClass.includes('last') || controlClass.includes('last') || dtIndex === 'last' || label === 'last') {
            return info.pages - 1;
        }

        const pageNumber = Number.parseInt(label, 10);

        if (!Number.isNaN(pageNumber)) {
            return pageNumber - 1;
        }

        return null;
    }

    function handlePagingClick(event) {
        const now = Date.now();
        const target = event.target instanceof Element ? event.target : event.target?.parentElement;

        if (!target) {
            return;
        }

        const control = target.closest(
            '.master-table-area .dt-paging a, .master-table-area .dt-paging button, ' +
                '.master-table-area .dataTables_paginate a, .master-table-area .dataTables_paginate button'
        );

        if (!control) {
            return;
        }

        if (event.type === 'click' && now - lastPagingEventAt < 350) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            return;
        }

        const table = getDataTableFromPaging(control);

        if (!table) {
            return;
        }

        const item = control.closest('.page-item, .dt-paging-button, .paginate_button');

        if (
            control.disabled ||
            control.classList.contains('disabled') ||
            control.classList.contains('current') ||
            control.classList.contains('active') ||
            item?.classList.contains('disabled') ||
            item?.classList.contains('current') ||
            item?.classList.contains('active')
        ) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            return;
        }

        const info = getPageInfo(table);

        if (!info) {
            return;
        }

        const targetPage = resolvePagingTarget(info, control);

        if (targetPage === null || targetPage < 0 || targetPage >= info.pages || targetPage === info.page) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        table.page(targetPage).draw('page');
        lastPagingEventAt = now;
        scheduleResize();
    }

    function moveReportControls(card, selector, destination) {
        card.querySelectorAll(selector).forEach(function (control) {
            const pane = control.closest('.tab-pane');

            if (pane && pane.id) {
                control.dataset.masterReportPane = pane.id;
            }

            destination.appendChild(control);
        });
    }

    function syncReportControlVisibility(card, controls) {
        const activePane = card.querySelector('.tab-pane.active');
        const activePaneId = activePane ? activePane.id : '';

        controls.querySelectorAll('[data-master-report-pane]').forEach(function (control) {
            control.hidden = control.dataset.masterReportPane !== activePaneId;
        });
    }

    function scheduleResize() {
        if (resizeFrame !== null) {
            cancelAnimationFrame(resizeFrame);
        }

        resizeFrame = requestAnimationFrame(function () {
            resizeFrame = null;
            resizeAll();
        });
    }

    function handleTabShown() {
        requestAnimationFrame(function () {
            adjustDataTables();

            scheduleResize();
        });
    }

    function initialize() {
        const page = document.querySelector(pageSelector);

        if (!page) {
            return;
        }

        document.documentElement.classList.add('master-table-page-active');
        document.body.classList.add('master-table-page-active');

        const mutationObserver = new MutationObserver(scheduleResize);
        mutationObserver.observe(page, {
            attributes: true,
            attributeFilter: ['class', 'style'],
            childList: true,
            subtree: true,
        });

        bindDataTableEvents();

        if ('ResizeObserver' in window) {
            const resizeObserver = new ResizeObserver(scheduleResize);
            document.querySelectorAll(areaSelector).forEach(function (area) {
                resizeObserver.observe(area);
            });
        }

        window.addEventListener('resize', scheduleResize);
        window.addEventListener('admin:sidebar-resize', scheduleResize);
        document.addEventListener('shown.bs.tab', handleTabShown);
        document.addEventListener('pointerup', handlePagingClick, true);
        document.addEventListener('click', handlePagingClick, true);
        scheduleResize();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
