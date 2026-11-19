@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Tambah Dokumen Legal</h5>
                <a href="{{ route('dokumen_legal.index', $holding->holding_code) }}" class="btn btn-secondary btn-sm">
                    Kembali
                </a>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form id="form-dokumen-legal" method="POST"
                    action="{{ route('dokumen_legal.store', $holding->holding_code) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kategori Dokumen</label>
                        <div class="col-sm-4">
                            <select name="kategori_id" id="kategori_id"
                                class="form-select @error('kategori_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>

                                @foreach ($kategori as $dk)
                                    <option value="{{ $dk->id_kategori }}"
                                        {{ old('kategori_id') == $dk->id_kategori ? 'selected' : '' }}>
                                        {{ $dk->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>

                            @error('kategori_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3" id="subKategoriWrapper" style="display:none;">
                        <label class="col-sm-2 col-form-label">Sub Kategori Dokumen</label>
                        <div class="col-sm-4">
                            <select name="sub_kategori_id" id="sub_kategori_id"
                                class="form-select @error('sub_kategori_id') is-invalid @enderror">
                                <option value="">-- Pilih Sub Kategori --</option>
                            </select>

                            @error('sub_kategori_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3" id="penerbitWrapper">
                        <label class="col-sm-2 col-form-label">
                            Penerbit Dokumen <span class="text-danger">*</span>
                        </label>

                        <div class="col-sm-4">

                            <!-- readonly jika hanya 1 penerbit -->
                            <input type="text" id="penerbit_readonly" class="form-control" readonly
                                style="display:none;">

                            <!-- dropdown jika >1 penerbit -->
                            <select name="penerbit_id" id="penerbit_select"
                                class="form-select @error('penerbit_id') is-invalid @enderror" style="display:none;">
                                <option value="">-- Pilih Penerbit --</option>
                            </select>

                            <!-- info jika tidak ada penerbit -->
                            <small id="penerbit_hint" class="text-muted d-none">
                                Penerbit belum tersedia. Silakan tambahkan di menu <b>Penerbit Dokumen</b>.
                            </small>

                            @error('penerbit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Dokumen Pengganti?</label>
                        <div class="col-sm-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_dokumen_pengganti"
                                    name="is_dokumen_pengganti" value="1"
                                    {{ old('is_dokumen_pengganti') == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_dokumen_pengganti">
                                    Ya, dokumen ini menggantikan dokumen lama
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3" id="field_dokumen_reference"
                        style="{{ old('is_dokumen_pengganti') == 1 ? '' : 'display:none;' }}">
                        <label class="col-sm-2 col-form-label">Dokumen Yang Digantikan</label>
                        <div class="col-sm-6">
                            <select name="dokumen_reference" id="dokumen_reference"
                                class="form-select @error('dokumen_reference') is-invalid @enderror">
                                <option value="">-- Pilih Dokumen --</option>
                            </select>

                            @error('dokumen_reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Dokumen</label>
                        <div class="col-sm-6">
                            <input type="text" name="nomer_dukumen"
                                class="form-control @error('nomer_dukumen') is-invalid @enderror"
                                value="{{ old('nomer_dukumen') }}">
                            @error('nomer_dukumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama Dokumen</label>
                        <div class="col-sm-6">
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama') }}">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Site</label>
                        <div class="col-sm-4">
                            <select name="site_id" class="form-select @error('site_id') is-invalid @enderror">
                                <option value="">-- Pilih Site --</option>

                                @foreach ($sites as $s)
                                    <option value="{{ $s->id }}" {{ old('site_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->site_name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-4">
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Awal</label>
                        <div class="col-sm-4">
                            <input type="date" name="tanggal_awal"
                                class="form-control @error('tanggal_awal') is-invalid @enderror"
                                value="{{ old('tanggal_awal') }}">
                            @error('tanggal_awal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Akhir</label>
                        <div class="col-sm-4">
                            <input type="date" name="tanggal_akhir"
                                class="form-control @error('tanggal_akhir') is-invalid @enderror"
                                value="{{ old('tanggal_akhir') }}">
                            @error('tanggal_akhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-6">
                            <input type="text" name="keterangan"
                                class="form-control @error('keterangan') is-invalid @enderror"
                                value="{{ old('keterangan') }}">
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">File Dokumen</label>
                        <div class="col-sm-6">
                            <input type="file" name="file"
                                class="form-control @error('file') is-invalid @enderror">
                            <small class="text-muted">PDF / DOC / DOCX (max 5MB)</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <button id="btn-submit" type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.getElementById('form-dokumen-legal').addEventListener('submit', function() {
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            document.getElementById('btn-submit').disabled = true;
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const kategoriSelect = document.getElementById('kategori_id');
            const subWrapper = document.getElementById('subKategoriWrapper');
            const subSelect = document.getElementById('sub_kategori_id');

            function resetSubKategori() {
                subSelect.innerHTML = '<option value="">-- Pilih Sub Kategori --</option>';
                subWrapper.style.display = 'none';
            }

            kategoriSelect.addEventListener('change', function() {
                const kategoriId = this.value;
                resetSubKategori();

                if (!kategoriId) return;

                fetch(`/dokumen-legal/ajax/sub-kategori/${kategoriId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            subWrapper.style.display = 'flex';

                            data.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.id_sub;
                                opt.textContent = item.sub_name;
                                subSelect.appendChild(opt);
                            });
                        }
                    })
                    .catch(() => resetSubKategori());
            });

            // support old value (jika validation error)
            @if (old('kategori_id'))
                kategoriSelect.dispatchEvent(new Event('change'));
            @endif
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const kategori = document.getElementById('kategori_id');
            const wrapper = document.getElementById('penerbitWrapper');
            const readonly = document.getElementById('penerbit_readonly');
            const select = document.getElementById('penerbit_select');
            const hint = document.getElementById('penerbit_hint');

            function reset() {
                // reset readonly
                readonly.value = '';
                readonly.style.display = 'none';

                // reset select
                select.innerHTML = '<option value="">-- Pilih Penerbit --</option>';
                select.style.display = 'none';
                select.required = false;

                // reset hint
                hint.classList.add('d-none');

                // hapus SEMUA hidden penerbit_id lama
                wrapper.querySelectorAll('input[name="penerbit_id"]').forEach(el => el.remove());
            }

            function loadPenerbit(kategoriId) {
                reset();
                if (!kategoriId) return;

                fetch(`/dokumen-legal/ajax/penerbit/${kategoriId}`)
                    .then(res => res.json())
                    .then(data => {

                        // TIDAK ADA PENERBIT
                        if (data.length === 0) {
                            hint.classList.remove('d-none');
                            return;
                        }

                        // SATU PENERBIT
                        if (data.length === 1) {
                            readonly.value = data[0].nama_penerbit;
                            readonly.style.display = 'block';

                            const hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = 'penerbit_id';
                            hidden.value = data[0].id_penerbit;
                            wrapper.appendChild(hidden);
                            return;
                        }

                        // BANYAK PENERBIT
                        select.style.display = 'block';
                        select.required = true;

                        data.forEach(p => {
                            const opt = document.createElement('option');
                            opt.value = p.id_penerbit;
                            opt.textContent = p.nama_penerbit;
                            select.appendChild(opt);
                        });
                    })
                    .catch(() => {
                        reset();
                    });
            }

            // on change kategori
            kategori.addEventListener('change', function() {
                loadPenerbit(this.value);
            });

            // AUTO LOAD SAAT PAGE LOAD
            if (kategori.value) {
                loadPenerbit(kategori.value);
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const kategori = document.getElementById('kategori_id');
            const toggle = document.getElementById('is_dokumen_pengganti');
            const refWrapper = document.getElementById('field_dokumen_reference');
            const refSelect = $('#dokumen_reference');

            let select2Initialized = false;

            function initSelect2() {
                if (select2Initialized) return;

                refSelect.select2({
                    placeholder: '-- Pilih Dokumen --',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0, // ⬅️ BOLEH KOSONG
                    language: {
                        inputTooShort: () => 'Ketik untuk mencari dokumen',
                        searching: () => 'Mencari dokumen...',
                        noResults: () => 'Dokumen yang dicari tidak ditemukan'
                    },
                    ajax: {
                        url: "{{ url('/dokumen-legal/ajax/reference') }}",
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            return {
                                q: params.term || '', // ⬅️ keyword search
                                kategori_id: $('#kategori_id').val(),
                                holding_id: "{{ $holding->id }}"
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });

                select2Initialized = true;
            }

            function handleToggle() {
                if (toggle.checked) {
                    refWrapper.style.display = 'flex';

                    // kalau kategori sudah dipilih baru init
                    if ($('#kategori_id').val()) {
                        initSelect2();
                        refSelect.val(null).trigger('change');
                    }
                } else {
                    refWrapper.style.display = 'none';
                    refSelect.val(null).trigger('change');
                }
            }

            // event
            toggle.addEventListener('change', handleToggle);
            kategori.addEventListener('change', function() {
                // kalau toggle ON dan kategori berubah → refresh pilihan
                if (toggle.checked) {
                    initSelect2();
                    refSelect.val(null).trigger('change');
                }
                handleToggle();
            });

            // initial load
            handleToggle();
        });
    </script>
@endsection
