<script src="{{ asset('assets/assets_users/index.js') }}"></script>
<script src="{{ asset('assets/assets_users/js/jquery.js') }}"></script>
<script src="{{ asset('assets/assets_users/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/assets_users/js/settings.js') }}"></script>
<script src="{{ asset('assets/assets_users/js/custom.js') }}"></script>
<script src="{{ asset('assets/assets_users/js/dz.carousel.js') }}"></script><!-- Swiper -->
<script src="{{ asset('assets/assets_users/vendor/swiper/swiper-bundle.min.js') }}"></script><!-- Swiper -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<!-- Datatable -->
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>


<script>
    $(function() {

        var today = moment().format('YYYY-MM-DD');
        var month = moment().format('MM');
        var day = moment().format('D');
        var year = moment().format('YYYY');

        $('.month').val(month);
        // $('.month option:lt(' + month + ')').prop('disabled', true);

        // Relokasi seluruh modal dan offcanvas ke <body> agar terbebas dari stacking context parent
        $('.modal, .offcanvas').each(function() {
            if (!$(this).parent().is('body')) {
                $(this).appendTo('body');
            }
        });

        // Keep focus outside an offcanvas before Bootstrap applies aria-hidden.
        $(document).on('show.bs.offcanvas', '.offcanvas', function() {
            this._offcanvasFocusTrigger = document.activeElement;
        });

        $(document).on('hide.bs.offcanvas', '.offcanvas', function() {
            if (this.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });

        $(document).on('hidden.bs.offcanvas', '.offcanvas', function() {
            var trigger = this._offcanvasFocusTrigger;
            if (trigger && document.contains(trigger) && !trigger.closest('.offcanvas')) {
                trigger.focus();
            }
            this._offcanvasFocusTrigger = null;
        });

    });

    /**
     * Helper global untuk mengonversi error XHR / HTTP / Server menjadi pesan ramah pengguna.
     * Mencegah tampilan kode mentah 500, 419, 404, atau SQL trace.
     */
    window.getFriendlyErrorMessage = function(xhr, defaultMsg) {
        if (!xhr) return defaultMsg || 'Terjadi gangguan saat memproses data.';

        // Jika xhr berupa string langsung
        if (typeof xhr === 'string') {
            if (/SQLSTATE|Connection refused|Stack trace|PDOException|ErrorException|QueryException/i.test(xhr)) {
                return 'Terjadi gangguan pada sistem server. Silakan hubungi administrator.';
            }
            return xhr;
        }

        var status = xhr.status || (xhr.responseJSON ? xhr.responseJSON.code : null);

        if (status === 0 || xhr.statusText === 'abort') {
            return 'Gagal terhubung ke server. Periksa koneksi internet Anda lalu coba lagi.';
        }
        if (status === 419) {
            return 'Sesi Anda telah berakhir. Silakan muat ulang (refresh) halaman untuk melanjutkan.';
        }
        if (status === 401) {
            return 'Sesi login telah kedaluwarsa. Silakan login kembali.';
        }
        if (status === 403) {
            return 'Anda tidak memiliki hak akses untuk melakukan tindakan ini.';
        }
        if (status === 404) {
            return 'Data atau layanan yang diminta tidak ditemukan.';
        }
        if (status === 422) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var errs = Object.values(xhr.responseJSON.errors).flat();
                if (errs.length > 0) {
                    return errs.join('<br>');
                }
            }
            if (xhr.responseJSON && xhr.responseJSON.message) {
                return xhr.responseJSON.message;
            }
            return 'Data yang dimasukkan belum lengkap atau tidak valid.';
        }
        if (status >= 500) {
            var serverMsg = xhr.responseJSON ? xhr.responseJSON.message : null;
            if (serverMsg && typeof serverMsg === 'string') {
                if (/SQLSTATE|Connection refused|Stack trace|PDOException|ErrorException|QueryException/i.test(
                        serverMsg)) {
                    return 'Terjadi gangguan pada sistem server saat memproses data. Silakan coba lagi nanti atau hubungi administrator.';
                }
                return serverMsg;
            }
            return 'Terjadi gangguan pada server. Silakan coba beberapa saat lagi atau hubungi administrator.' + (
                    serverMsg ? '<br><br>' + serverMsg : '') + '<br><br>' + xhr.statusText + ' (' + xhr.status +
                ')' + '<br><br>' + xhr.responseText + '<br><br>'.serverMsg;
        }

        if (xhr.responseJSON && xhr.responseJSON.message) {
            var msg = xhr.responseJSON.message;
            if (typeof msg === 'string' &&
                /SQLSTATE|Connection refused|Stack trace|PDOException|ErrorException|QueryException/i.test(msg)) {
                return 'Terjadi gangguan pada sistem saat memproses data.';
            }
            return msg;
        }

        return defaultMsg || 'Terjadi gangguan saat memproses data. Silakan coba lagi.';
    };
</script>
