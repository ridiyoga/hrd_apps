@extends('admin.layouts.dashboard')

@section('isi')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Kategori Dokumen Legal</h5>
            <a href="{{ route('dokumen_legal.kategori.kategori_master.index', $holding->holding_code) }}"
                class="btn btn-secondary btn-sm">
                Kembali
            </a>
        </div>

        <div class="card-body">
            <form method="POST"
                action="{{ route('dokumen_legal.kategori.kategori_master.update', [$holding->holding_code, $kategoriDokumen->id_kategori]) }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">ID Kategori Dokumen</label>
                    <div class="col-sm-6">
                        <input type="text" name="id_kategori"
                            class="form-control @error('id_kategori') is-invalid @enderror"
                            value="{{ old('id_kategori', $kategoriDokumen->id_kategori) }}">
                        @error('id_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Nama Kategori Dokumen</label>
                    <div class="col-sm-6">
                        <input type="text" name="nama_kategori"
                            class="form-control @error('nama_kategori') is-invalid @enderror"
                            value="{{ old('nama_kategori', $kategoriDokumen->nama_kategori) }}">
                        @error('nama_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Reminder (Hari)</label>
                    <div class="col-sm-6">
                        <input type="number" name="reminder_days" class="form-control" min="1"
                            value="{{ old('reminder_days', $kategoriDokumen->reminder_days ?? 30) }}">
                        <small class="text-muted">
                            Notifikasi akan muncul X hari sebelum masa berlaku habis
                        </small>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary" id="btn_simpan">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.getElementById('form_kategori_dokumen_legal').addEventListener('submit', function() {
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
    });
</script>
@endsection