{{-- resources/views/admin/absensi.blade.php --}}

<x-admin-layout title="Data Absensi">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Data Absensi (Rekap IN/OUT)</h4>
            <div class="text-muted small">Menampilkan jam masuk & pulang per hari</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card shadow-soft mb-3">
        <div class="card-body">
            <form class="row g-2" method="GET" action="{{ route('admin.absensi.index') }}">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Tanggal</label>
                    <input type="date" name="date" value="{{ $date ?? '' }}" class="form-control rounded-4">
                </div>
                <div class="col-md-7">
                    <label class="form-label small text-muted mb-1">Cari</label>
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control rounded-4"
                        placeholder="Cari NIM / Nama / Prodi / Kelas...">
                </div>
                <div class="col-md-2 d-grid align-content-end">
                    <button class="btn btn-dark rounded-4 mt-md-4" type="submit">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-soft">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:70px;">#</th>
                            <th>Tanggal</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Prodi</th>
                            <th>Kelas</th>
                            <th>Masuk (IN)</th>
                            <th>Pulang (OUT)</th>
                            <th style="width:160px;" class="text-end">Foto</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($absensi as $i => $row)
                            @php
                                $mhs = $row['mahasiswa'] ?? null;
                                $in = $row['in'] ?? null;
                                $out = $row['out'] ?? null;

                                $badgeOut = $out ? 'bg-success' : 'bg-warning text-dark';
                                $labelOut = $out ? 'SUDAH OUT' : 'BELUM OUT';

                                $inUrl = ($in && $in->foto_path) ? \Illuminate\Support\Facades\Storage::url($in->foto_path) : null;
                                $outUrl = ($out && $out->foto_path) ? \Illuminate\Support\Facades\Storage::url($out->foto_path) : null;
                            @endphp

                            <tr>
                                <td>{{ $absensi->firstItem() + $i }}</td>
                                <td class="fw-semibold">{{ $row['tanggal'] ?? '-' }}</td>
                                <td>{{ $mhs->nim ?? '-' }}</td>
                                <td>{{ $mhs->nama ?? '-' }}</td>
                                <td>{{ $mhs->prodi ?? '-' }}</td>
                                <td>{{ $mhs->kelas ?? '-' }}</td>

                                <td>
                                    @if($in)
                                        <div class="fw-semibold">{{ $in->jam ?? '-' }}</div>
                                        <div class="small text-muted">
                                            @if(!empty($in->latitude) && !empty($in->longitude))
                                                {{ $in->latitude }}, {{ $in->longitude }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge {{ $badgeOut }} rounded-pill px-3 mb-1">{{ $labelOut }}</span>
                                    @if($out)
                                        <div class="fw-semibold">{{ $out->jam ?? '-' }}</div>
                                        <div class="small text-muted">
                                            @if(!empty($out->latitude) && !empty($out->longitude))
                                                {{ $out->latitude }}, {{ $out->longitude }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                                        @if($inUrl)
                                            <button class="btn btn-sm btn-outline-primary rounded-4 btn-foto" type="button"
                                                data-title="Foto Masuk (IN)" data-url="{{ $inUrl }}">
                                                <i class="bi bi-image"></i> IN
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary rounded-4" type="button" disabled>
                                                <i class="bi bi-image"></i> IN
                                            </button>
                                        @endif

                                        @if($outUrl)
                                            <button class="btn btn-sm btn-outline-primary rounded-4 btn-foto" type="button"
                                                data-title="Foto Pulang (OUT)" data-url="{{ $outUrl }}">
                                                <i class="bi bi-image"></i> OUT
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary rounded-4" type="button" disabled>
                                                <i class="bi bi-image"></i> OUT
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Belum ada data absensi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $absensi->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Foto --}}
    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoTitle">Foto Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="fotoPreview" src="" alt="Foto Absensi" class="img-fluid rounded-4">
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            console.log('[absensi] scripts loaded');

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-foto');
                if (!btn) return;

                const title = btn.getAttribute('data-title') || 'Foto Absensi';
                const url = btn.getAttribute('data-url') || '';

                console.log('[absensi] open modal', title, url);

                document.getElementById('fotoTitle').innerText = title;
                document.getElementById('fotoPreview').src = url;

                const modalEl = document.getElementById('modalFoto');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });
        </script>
    @endpush
</x-admin-layout>