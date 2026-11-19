/**
 * Main
 */

'use strict';

let menu, animate;

(function () {
  // Button & Pagination Waves effect
  if (typeof Waves !== 'undefined') {
    Waves.init();
    Waves.attach(
      ".btn[class*='btn-']:not(.position-relative):not([class*='btn-outline-']):not([class*='btn-label-'])",
      ['waves-light']
    );
    Waves.attach("[class*='btn-outline-']:not(.position-relative)");
    Waves.attach('.pagination .page-item .page-link');
    Waves.attach('.dropdown-menu .dropdown-item');
    Waves.attach('.light-style .list-group .list-group-item-action');
    Waves.attach('.dark-style .list-group .list-group-item-action', ['waves-light']);
    Waves.attach('.nav-tabs:not(.nav-tabs-widget) .nav-item .nav-link');
    Waves.attach('.nav-pills .nav-item .nav-link', ['waves-light']);
    Waves.attach('.menu-vertical .menu-item .menu-link.menu-toggle');
  }

  // Window scroll function for navbar
  function onScroll() {
    var layoutPage = document.querySelector('.layout-page');
    if (layoutPage) {
      if (window.pageYOffset > 0) {
        layoutPage.classList.add('window-scrolled');
      } else {
        layoutPage.classList.remove('window-scrolled');
      }
    }
  }
  // On load time out
  setTimeout(() => {
    onScroll();
  }, 200);

  // On window scroll
  window.onscroll = function () {
    onScroll();
  };

  // Initialize menu
  //-----------------

  let layoutMenuEl = document.querySelectorAll('#layout-menu');
  layoutMenuEl.forEach(function (element) {
    menu = new Menu(element, {
      orientation: 'vertical',
      closeChildren: false
    });
    // Change parameter to true if you want scroll animation
    window.Helpers.scrollToActive((animate = false));
    window.Helpers.mainMenu = menu;
  });

  let sidebarResizeTimer = null;
  let sidebarTableSyncTimers = [];

  function adjustVisibleTables() {
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.dataTable) {
      const tables = window.jQuery.fn.dataTable.tables({ visible: true, api: true });

      tables.columns.adjust();

      if (tables.responsive && typeof tables.responsive.recalc === 'function') {
        tables.responsive.recalc();
      }
    }

    $(window).trigger('admin:sidebar-resize');
  }

  function syncSidebarTables() {
    sidebarTableSyncTimers.forEach(window.clearTimeout);
    sidebarTableSyncTimers = [];

    [0, 80, 160, 260, 340].forEach(function (delay) {
      sidebarTableSyncTimers.push(
        window.setTimeout(function () {
          window.requestAnimationFrame(adjustVisibleTables);
        }, delay)
      );
    });
  }

  function queueSidebarResize() {
    window.clearTimeout(sidebarResizeTimer);
    sidebarResizeTimer = window.setTimeout(function () {
      $(window).trigger('resize');
      adjustVisibleTables();
    }, 310);
  }

  function setDesktopSidebarCollapsed(collapsed) {
    const $html = $('html');
    const $layoutMenu = $('#layout-menu');
    const wasCollapsed = $html.hasClass('layout-menu-collapsed');

    $html.toggleClass('layout-menu-collapsed', collapsed);
    $html.removeClass('layout-menu-hover');
    $layoutMenu.toggleClass('menu-collapsed', collapsed);
    syncSidebarTables();

    if (wasCollapsed !== collapsed) {
      queueSidebarResize();
    }
  }

  if (!window.Helpers.isSmallScreen()) {
    setDesktopSidebarCollapsed(true);
  }

  let sidebarWasSmallScreen = window.Helpers.isSmallScreen();

  $(window).on('resize.sidebarAdmin', function () {
    const isSmallScreen = window.Helpers.isSmallScreen();

    if (isSmallScreen === sidebarWasSmallScreen) {
      return;
    }

    sidebarWasSmallScreen = isSmallScreen;

    if (isSmallScreen) {
      $('html').removeClass('layout-menu-collapsed layout-menu-hover');
      $('#layout-menu').removeClass('menu-collapsed');
      return;
    }

    setDesktopSidebarCollapsed(true);
  });

  // Initialize menu togglers and bind click on each
  $('.layout-menu-toggle').on('click.sidebarAdmin', function (event) {
    event.preventDefault();

    if (window.Helpers.isSmallScreen()) {
      window.Helpers.toggleCollapsed();
    }
  });

  const $desktopSidebar = $('#layout-menu');

  if ($desktopSidebar.length) {
    $desktopSidebar.on('mouseenter.sidebarAdmin', function () {
      if (!window.Helpers.isSmallScreen()) {
        setDesktopSidebarCollapsed(false);
      }
    });

    $desktopSidebar.on('mouseleave.sidebarAdmin', function () {
      if (!window.Helpers.isSmallScreen()) {
        setDesktopSidebarCollapsed(true);
      }
    });
  }

  // Display in main menu when menu scrolls
  let menuInnerContainer = document.getElementsByClassName('menu-inner'),
    menuInnerShadow = document.getElementsByClassName('menu-inner-shadow')[0];
  if (menuInnerContainer.length > 0 && menuInnerShadow) {
    menuInnerContainer[0].addEventListener('ps-scroll-y', function () {
      if (this.querySelector('.ps__thumb-y').offsetTop) {
        menuInnerShadow.style.display = 'block';
      } else {
        menuInnerShadow.style.display = 'none';
      }
    });
  }

  // Init helpers & misc
  // --------------------

  // Init BS Tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Accordion active class
  const accordionActiveFunction = function (e) {
    if (e.type == 'show.bs.collapse' || e.type == 'show.bs.collapse') {
      e.target.closest('.accordion-item').classList.add('active');
    } else {
      e.target.closest('.accordion-item').classList.remove('active');
    }
  };

  const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
  const accordionList = accordionTriggerList.map(function (accordionTriggerEl) {
    accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
    accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
  });

  // Auto update layout based on screen size
  window.Helpers.setAutoUpdate(true);

  // Toggle Password Visibility
  window.Helpers.initPasswordToggle();

  // Speech To Text
  window.Helpers.initSpeechToText();

  // Nav tabs animation
  window.Helpers.navTabsAnimation();

  // Manage menu expanded/collapsed with templateCustomizer & local storage
  //------------------------------------------------------------------

  // If current layout is horizontal OR current window screen is small (overlay menu) than return from here
  if (window.Helpers.isSmallScreen()) {
    return;
  }

  // If current layout is vertical and current window screen is > small

  // Auto update menu collapsed/expanded based on the themeConfig
  window.Helpers.setCollapsed(true, false);
})();
