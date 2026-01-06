@extends('mahasiswa.layout')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h4 class="fw-bold mb-1">Riwayat Absensi</h4>
        <div class="text-muted small">Rekap absensi Masuk (IN) dan Pulang (OUT) per tanggal.</div>
    </div>

    {{-- FILTER --}}
    <div class="card shadow-soft border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('mahasiswa.riwayat') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ $dari }}" class="form-control rounded-4">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ $sampai }}" class="form-control rounded-4">
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button class="btn btn-primary rounded-4">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('mahasiswa.riwayat') }}" class="btn btn-outline-secondary rounded-4">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-soft border-0">
        <div class="card-body">

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-semibold mb-0">Rekap</h5>
                <div class="small text-muted">Total: {{ $rekap->count() }} hari</div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center small">
                    <thead class="table-light">
                        <tr>
                            <th style="width:140px;">Tanggal</th>
                            <th style="width:110px;">Jam IN</th>
                            <th style="width:110px;">Jam OUT</th>
                            <th>Foto</th>
                            <th style="width:160px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($rekap->count() === 0)
                            <tr>
                                <td colspan="5" class="text-muted">Belum ada data absensi.</td>
                            </tr>
                        @endif

                        @foreach($rekap as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}</td>
                                <td class="fw-semibold">{{ $row['jam_in'] }}</td>
                                <td class="fw-semibold">{{ $row['jam_out'] }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        @if($row['foto_in'])
                                            <button class="btn btn-sm btn-outline-primary rounded-4"
                                                    onclick="previewFoto('{{ asset('storage/'.$row['foto_in']) }}', 'Foto Absen IN')">
                                                IN
                                            </button>
                                        @else
                                            <span class="text-muted">IN -</span>
                                        @endif

                                        @if($row['foto_out'])
                                            <button class="btn btn-sm btn-outline-danger rounded-4"
                                                    onclick="previewFoto('{{ asset('storage/'.$row['foto_out']) }}', 'Foto Absen OUT')">
                                                OUT
                                            </button>
                                        @else
                                            <span class="text-muted">OUT -</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $badge = 'bg-secondary';
                                        if ($row['status'] === 'Menunggu OUT') $badge = 'bg-warning text-dark';
                                        if ($row['status'] === 'Lengkap (IN/OUT)') $badge = 'bg-success';
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $row['status'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

{{-- MODAL FOTO --}}
<div class="modal fade" id="modalFoto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFotoTitle">Preview Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img id="fotoFrame" src="" class="img-fluid rounded-4 w-100" alt="Foto Absensi">
            </div>
        </div>
    </div>
</div>

<script>
    function previewFoto(src, title) {
        document.getElementById('fotoFrame').src = src;
        document.getElementById('modalFotoTitle').innerText = title;
        new bootstrap.Modal(document.getElementById('modalFoto')).show();
    }
</script>
@endsection
