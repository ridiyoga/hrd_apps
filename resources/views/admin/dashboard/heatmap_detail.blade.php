@if ($docs->isEmpty())
    <div class="text-center text-muted py-4">
        Tidak ada dokumen pada tanggal ini
    </div>
@else
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Nama</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($docs as $doc)
                <tr>
                    <td>{{ $doc->nomer_dukumen }}</td>
                    <td>{{ $doc->nama }}</td>
                    <td>
                        <span
                            class="badge bg-{{ $doc->status === 'expired' ? 'danger' : ($doc->status === 'aktif' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($doc->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
