<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>BERAS KEDIRI</title>
    <meta name="description" content="Website company profile Beras Kediri." />
    <meta name="google-site-verification" content="-Fzqcg-5HaD8whjp-kvt0jwdOPBQIcT9Dhc_E39g3ik" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        :root {
            --bg: #ffffff;
            --text: #0b1220;
            --muted: #6b7280;
            --purple-600: #6d28d9;
            /* main accent */
            --purple-400: #a78bfa;
            --gold: #f59e0b;
            --radius: 12px;
            --container: 1100px;
        }

        .swiper {
            padding: 40px 10px;
        }

        .swiper-slide {
            display: flex;
            justify-content: center;
        }

        * {
            box-sizing: border-box
        }

        body {
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            -webkit-font-smoothing: antialiased
        }

        a {
            color: var(--purple-600)
        }

        /* NAV */
        .navbar {
            transition: all 0.4s ease;
            background: transparent;
            box-shadow: none;
            padding: 1.2rem 0;
        }

        /* Saat di-scroll: ubah background, tinggi, dan tambahkan shadow */
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(6px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 0.7rem 0;
            transition: all 0.4s ease;
        }

        .navbar.scrolled .nav-link {
            color: #5a2d91 !important;
            /* ungu elegan */
        }

        .navbar.scrolled .navbar-brand {
            color: #5a2d91 !important;
            transform: scale(1.05);
        }

        .brand-logo {
            font-weight: 800;
            color: var(--purple-600);
            letter-spacing: 0.6px
        }

        .nav-link {
            color: var(--muted) !important;
            font-weight: 600
        }

        .btn-cta {
            background: linear-gradient(90deg, var(--purple-600), var(--purple-400));
            color: white;
            border-radius: 10px;
            padding: 8px 14px;
            font-weight: 700
        }

        /* HERO */
        .hero {
            padding: 60px 0
        }

        .hero-card {
            background: #fbfbff;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(16, 24, 40, 0.06);
            overflow: hidden
        }

        .hero-left {
            padding: 42px
        }

        .kicker {
            color: var(--purple-600);
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.6px
        }

        .hero-title {
            font-size: 34px;
            color: var(--text);
            margin-top: 10px
        }

        .hero-desc {
            color: var(--muted);
            margin-top: 12px
        }

        /* VIDEO PROFILE */
        .video-profile {
            padding: 70px 0;
            background: linear-gradient(180deg, #fbfbff, #ffffff);
        }

        .video-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 10px 30px rgba(128, 0, 255, 0.12);
            transition: all 0.3s ease;
        }

        .video-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(128, 0, 255, 0.18);
        }

        .video-card iframe {
            border-radius: 12px;
        }



        /* Product */
        .products {
            padding: 70px 0
        }

        .product-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(128, 0, 255, 0.08);
            padding: 20px;
            max-width: 260px;
            text-align: center;
            transition: all 0.4s ease;
        }

        /* Saat terlihat di layar */
        .product-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Delay berurutan untuk efek elegan */
        .product-card:nth-child(1) {
            transition-delay: 0.1s;
        }

        .product-card:nth-child(2) {
            transition-delay: 0.25s;
        }

        .product-card:nth-child(3) {
            transition-delay: 0.4s;
        }

        .product-card:nth-child(4) {
            transition-delay: 0.55s;
        }

        /* Hover lembut */
        .product-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 6px 20px rgba(128, 0, 255, 0.15);
            transition: all 0.3s ease;
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: contain
        }

        .price {
            font-weight: 800;
            color: var(--purple-600)
        }

        /* About */
        .about {
            padding: 60px 0;
            background: linear-gradient(180deg, #fff, #fbfbff)
        }

        /* Contact */
        .contact {
            padding: 60px 0
        }

        footer {
            padding: 28px 0;
            color: var(--muted);
            text-align: center
        }

        @media (max-width: 991px) {
            .hero {
                padding: 28px 0
            }

            .hero-title {
                font-size: 26px
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .product-card {
                max-width: 200px;
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top">
        <div class="container" style="max-width:var(--container)">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="{{ asset('public/assets/img/logosp.png') }}" width="40" height="40" alt="logo"
                    style="border-radius:8px">
                <span class="brand-logo">BERAS KEDIRI</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="#home">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#products">Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Kontak</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://karir.sumberpangan.store">Karir</a></li>
                    <!--<li class="nav-item"><a class="nav-link" href="/">Karir</a></li>-->
                    <li class="nav-item ms-3 d-none d-lg-block"><a class="btn btn-cta" href="tel:+6281133357407"><i
                                class="bi bi-telephone-fill"></i>&nbsp;081133357407</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section id="home" class="hero">
        <div class="container" style="max-width:var(--container)">
            <div class="hero-card d-flex align-items-stretch gap-0">
                <div class="hero-left col-lg-6">
                    <div class="kicker">SELAMAT DATANG</div>
                    <h1 class="hero-title">di Beras Kediri — Beras Pilihan Keluarga</h1>
                    <p class="hero-desc">Perusahaan pengolahan beras terkemuka yang senantiasa memberikan kualitas beras
                        terbaik untuk konsumen, dan berkontribusi meningkatkan kesejahteraan petani.</p>

                    <div class="mt-4 d-flex gap-2">
                        <a href="#products" class="btn btn-cta">Beli Sekarang</a>
                        <a href="#about" class="btn btn-outline-secondary"
                            style="border-radius:10px;padding:8px 14px">Baca Selengkapnya</a>
                    </div>

                    <div class="mt-4 d-flex gap-3">
                        <div>
                            <div style="font-weight:700;color:var(--text)">Garansi Mutu</div>
                            <div style="color:var(--muted);font-size:13px">Kontrol kualitas ketat setiap batch</div>
                        </div>
                        <div>
                            <div style="font-weight:700;color:var(--text)">Pengiriman Cepat</div>
                            <div style="color:var(--muted);font-size:13px">Jangkauan nasional</div>
                        </div>
                    </div>
                </div>

                <div class="d-none d-lg-block col-lg-6" style="padding:0">
                    <img src="{{ asset('public/assets/img/hero-img.png') }}" alt="hero"
                        style="width:100%;height:100%;object-fit:cover">
                </div>
            </div>
        </div>
    </section>
    <!-- ANAK PERUSAHAAN -->
    <section id="subsidiary" class="video-profile">
        <div class="container" style="max-width:var(--container)">
            <div class="text-center mb-4">
                <div style="color:var(--purple-600);font-weight:700;letter-spacing:1px">
                    VIDEO PROFIL
                </div>
                <h2 style="font-weight:800">Mengenal Beras Kediri</h2>
                <p class="text-muted mt-2">
                    Sekilas proses, komitmen, dan kualitas Beras Kediri
                </p>
            </div>

            <div class="row g-4">
                <!-- Anak Perusahaan 1 -->
                <div class="col-md-6">
                    <div class="video-card h-100">
                        <h5 class="fw-bold mb-2">CV. SUMBER PANGAN</h5>

                        <div class="ratio ratio-16x9">
                            <video controls poster="{{ asset('public/assets/img/about.png') }}"
                                style="width:100%;border-radius:12px">
                                <source src="{{ asset('public/assets/video/company_profile1.mp4') }}" type="video/mp4">
                                Browser Anda tidak mendukung video.
                            </video>
                        </div>
                    </div>
                </div>

                <!-- Anak Perusahaan 2 -->
                <div class="col-md-6">
                    <div class="video-card h-100">
                        <h5 class="fw-bold mb-2">PT. SURYA PANGAN SEMESTA</h5>

                        <div class="ratio ratio-16x9">
                            <video controls poster="{{ asset('public/assets/img/about.png') }}"
                                style="width:100%;border-radius:12px">
                                <source src="{{ asset('public/assets/video/company_profile2.mp4') }}"
                                    type="video/mp4">
                                Browser Anda tidak mendukung video.
                            </video>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- PRODUCTS -->
    <section id="products" class="products">
        <div class="container" style="max-width:var(--container)">
            <div class="text-center mb-5">
                <div style="color:var(--purple-600);font-weight:700;letter-spacing:1px">PRODUK</div>
                <h2 style="font-weight:800">Beras Kediri</h2>
            </div>

            <div class="swiper myProductSwiper">
                <div class="swiper-wrapper">
                    <!-- sample product cards -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="{{ asset('public/assets/img/values-sedapwangi.png') }}" alt="Sedap Wangi">
                            <div>
                                <h5 class="mt-3 mb-1">Sedap Wangi</h5>
                                <p class="text-muted small">Beras premium, aroma harum, pulen sempurna.</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <button class="btn btn-sm btn-light me-2" data-bs-toggle="modal"
                                            data-bs-target="#productModal" data-product="Sedap Wangi"
                                            style=" border-radius: 10px; padding: 8px 14px; font-weight: 700">Detail</button>
                                        <button class="btn btn-sm btn-cta">Tanya</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="{{ asset('public/assets/img/values-tanak.png') }}" alt="Tanah">
                            <div>
                                <h5 class="mt-3 mb-1">Tanak</h5>
                                <p class="text-muted small">Beras kualitas ekspor, tekstur lembut.</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <button class="btn btn-sm btn-light me-2" data-bs-toggle="modal"
                                            data-bs-target="#productModal" data-product="Tanak"
                                            style=" border-radius: 10px; padding: 8px 14px; font-weight: 700">Detail</button>
                                        <button class="btn btn-sm btn-cta">Tanya</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="{{ asset('public/assets/img/values-lahap.png') }}" alt="Lahap">
                            <div>
                                <h5 class="mt-3 mb-1">Lahap</h5>
                                <p class="text-muted small">Beras keluarga, pas di segala masakan.</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <button class="btn btn-sm btn-light me-2" data-bs-toggle="modal"
                                            data-bs-target="#productModal" data-product="Lahap"
                                            style=" border-radius: 10px; padding: 8px 14px; font-weight: 700">Detail</button>
                                        <button class="btn btn-sm btn-cta">Tanya</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="{{ asset('public/assets/img/values-ngawitimas.png') }}" alt="Ngawiti Mas">
                            <div>
                                <h5 class="mt-3 mb-1">Ngawiti Mas</h5>
                                <p class="text-muted small">Varian ekonomis, rasa konsisten.</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <button class="btn btn-sm btn-light me-2" data-bs-toggle="modal"
                                            data-bs-target="#productModal" data-product="Ngawiti Mas"
                                            style=" border-radius: 10px; padding: 8px 14px; font-weight: 700">Detail</button>
                                        <button class="btn btn-sm btn-cta">Tanya</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="about">
        <div class="container" style="max-width:var(--container)">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3 style="color:var(--purple-600)">Tentang CV. Sumber Pangan</h3>
                    <h4 style="font-weight:800">Komitmen pada Kualitas & Petani</h4>
                    <p class="text-muted">Perusahaan yang bergerak di bidang pengolahan dan penyosohan beras. CV Sumber
                        Pangan memiliki kantor di Jl. Raya Sambirobyong Rt.001 Rw. 003 Sambirobyong Kayen KIdul, Kab.
                        Kediri.</p>
                    <p>Memproduksi dan menawarkan produk dengan kualitas terbaik, higienis dibidang pangan. Memberikan
                        layanan prima yang berorientasi terhadap pemenuhan kebutuhan. Senantiasa melakukan perbaikan
                        berkesinambungan untuk produk.</p>
                </div>
                <div class="col-lg-6">
                    <img src="{{ asset('public/assets/img/about.png') }}" alt="about"
                        style="width:100%;border-radius:12px;object-fit:cover">
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT -->
    <section id="contact" class="contact">
        <div class="container" style="max-width:var(--container)">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h4>Kontak Kami</h4>
                    <p class="text-muted">Hubungi tim kami untuk pemesanan, kerja sama, atau pertanyaan lainnya.</p>
                    <form id="contactForm">
                        <div class="mb-2"><input class="form-control" name="name" placeholder="Nama" required>
                        </div>
                        <div class="mb-2"><input class="form-control" name="email" type="email"
                                placeholder="Email" required></div>
                        <div class="mb-2">
                            <textarea class="form-control" name="message" rows="4" placeholder="Pesan" required></textarea>
                        </div>
                        <div>
                            <button class="btn btn-cta" type="submit">Kirim Pesan</button>
                            <button class="btn btn-outline-secondary ms-2" type="reset">Reset</button>
                        </div>
                    </form>
                </div>

                <div class="col-lg-6">
                    <h5>Alamat</h5>
                    <p class="text-muted">Sambiroto, Jl. Raya Sambirobyong 88 Sambiroto Sambirobyong, Sambiroto, Kayen
                        Kidul, Kec. Kayen Kidul, Kabupaten Kediri</p>
                    <div class="ratio ratio-16x9" style="border-radius:8px;overflow:hidden">
                        <!-- placeholder map, replace with embed from Google Maps -->
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.298026710903!2d112.09331287574986!3d-7.758183176937976!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e785b469d322b8b%3A0xbf402a81eaa858a5!2sCV.%20Sumber%20Pangan!5e0!3m2!1sid!2sid!4v1761965204136!5m2!1sid!2sid"
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            frameborder="0" style="border:0;width:100%;height:100%"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container" style="max-width:var(--container)">
            <div class="d-flex justify-content-between align-items-center">
                <div>© CV. Sumber Pangan — Beras Kediri</div>
                <div class="text-muted small">Design by IT SP & SPS Kediri</div>
            </div>
        </div>
    </footer>

    <!-- Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Detail Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6"><img id="modalImage" src=""
                                style="width:100%;border-radius:8px;object-fit:cover"></div>
                        <div class="col-md-6">
                            <h4 id="modalTitle"></h4>
                            <p id="modalDesc" class="text-muted"></p>
                            <div id="modalVariant" style="font-weight:800;color:var(--purple-600)"></div>
                            <div class="mt-3">
                                <a href="https://wa.me/6281133357407?text=Halo%20Beras%20Lahap..%0APerkenalkan%20saya%2C%20%0A%0ANama%20%3A%20%0ADomilisi%20%2F%20Wilayah%20%3A%20%0A%0AMau%20bertanya%20dong%20min%20%3F"
                                    type="button" class="btn btn-cta btn-outline-secondary ms-2">Tanya</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        const swiper = new Swiper(".myProductSwiper", {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                576: {
                    slidesPerView: 2
                },
                992: {
                    slidesPerView: 3
                },
                1200: {
                    slidesPerView: 4
                }
            }
        });
        const PRODUCTS = {
            'Sedap Wangi': {
                title: 'Sedap Wangi',
                desc: 'Beras premium, aroma harum, pulen sempurna.',
                variant: 'Varian : 5Kg, 10Kg, 20Kg, 25Kg',
                img: '{{ asset('public/assets/img/values-sedapwangi.png') }}'
            },
            'Tanak': {
                title: 'Tanak',
                desc: 'Beras kualitas ekspor, tekstur lembut.',
                variant: 'Varian : 5Kg, 10Kg, 20Kg, 25Kg',
                img: '{{ asset('public/assets/img/values-tanak.png') }}'
            },
            'Lahap': {
                title: 'Lahap',
                desc: 'Beras keluarga, pas di segala masakan.',
                variant: 'Varian : 5Kg, 10Kg, 20Kg, 25Kg',
                img: '{{ asset('public/assets/img/values-lahap.png') }}'
            },
            'Ngawiti Mas': {
                title: 'Ngawiti Mas',
                desc: 'Varian ekonomis, rasa konsisten.',
                variant: 'Varian : 5Kg, 10Kg, 20Kg, 25Kg',
                img: '{{ asset('public/assets/img/values-ngawitimas.png') }}'
            }
        };

        const productModal = document.getElementById('productModal');
        productModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const key = button.getAttribute('data-product');
            const data = PRODUCTS[key] || {
                title: 'Produk',
                desc: '-',
                variant: '-',
                img: ''
            };
            document.getElementById('modalTitle').textContent = data.title;
            document.getElementById('modalDesc').textContent = data.desc;
            document.getElementById('modalVariant').textContent = data.variant;
            document.getElementById('modalImage').src = data.img;
        });

        // contact form demo
        $('#contactForm').on('submit', function(e) {
            e.preventDefault();
            const name = $(this).find('[name="name"]').val();
            alert('Terima kasih ' + name + '! Pesan Anda telah terkirim.');
            this.reset();
        });
    </script>
    <script>
        $(window).on("scroll", function() {
            if ($(window).scrollTop() > 50) {
                $(".navbar").addClass("scrolled");
            } else {
                $(".navbar").removeClass("scrolled");
            }
            $(".product-card").each(function() {
                const topOfWindow = $(window).scrollTop() + $(window).height();
                const elementTop = $(this).offset().top + 100;

                if (topOfWindow > elementTop) {
                    $(this).addClass("visible");
                }
            });
        });
    </script>
</body>

</html>
