{{-- resources/views/admin/lokasi.blade.php --}}

<x-admin-layout title="Lokasi UNPAM">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Lokasi UNPAM</h4>
            <div class="text-muted small">Daftarkan titik kampus (Latitude/Longitude) & radius validasi</div>
        </div>

        <button class="btn btn-primary rounded-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Lokasi
        </button>
    </div>

    {{-- Error validation --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm">
            <b>Gagal menyimpan:</b>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filter --}}
    <div class="card shadow-soft mb-3">
        <div class="card-body">
            <form class="row g-2" method="GET" action="{{ route('admin.lokasi.index') }}">
                <div class="col-md-10">
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control rounded-4"
                           placeholder="Cari nama lokasi / latitude / longitude...">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-dark rounded-4" type="submit">
                        <i class="bi bi-search me-1"></i> Cari
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
                            <th>Nama Lokasi</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Radius (m)</th>
                            <th>Aktif</th>
                            <th style="width:220px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lokasi as $i => $l)
                            <tr>
                                <td>{{ $lokasi->firstItem() + $i }}</td>
                                <td class="fw-semibold">{{ $l->nama_lokasi }}</td>
                                <td>{{ $l->latitude }}</td>
                                <td>{{ $l->longitude }}</td>
                                <td>{{ $l->radius_meter }}</td>
                                <td>
                                    @if($l->is_active)
                                        <span class="badge bg-success rounded-pill px-3">AKTIF</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3">NONAKTIF</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                                        @if(!$l->is_active)
                                            <form method="POST" action="{{ route('admin.lokasi.active', $l->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-4">
                                                    <i class="bi bi-check2-circle"></i> Jadikan Aktif
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-success rounded-4" type="button" disabled>
                                                <i class="bi bi-check2-circle"></i> Aktif
                                            </button>
                                        @endif

                                        <button class="btn btn-sm btn-outline-primary rounded-4"
                                                type="button"
                                                onclick='openEditModal(@json($l))'>
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger rounded-4"
                                                type="button"
                                                onclick="openDeleteModal({{ $l->id }}, @json($l->nama_lokasi))">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada lokasi. Silakan tambah lokasi UNPAM.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $lokasi->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL: Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.lokasi.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Lokasi UNPAM</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lokasi</label>
                                <input name="nama_lokasi" class="form-control rounded-4" placeholder="UNPAM Viktor / Kampus 1 / dll" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Latitude</label>
                                <input name="latitude" class="form-control rounded-4" placeholder="-6.3xxxxxx" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Longitude</label>
                                <input name="longitude" class="form-control rounded-4" placeholder="106.7xxxxxx" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Radius (meter)</label>
                                <input name="radius_meter" type="number" min="10" max="5000" value="200"
                                       class="form-control rounded-4" required>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="add_active" value="1">
                                    <label class="form-check-label" for="add_active">
                                        Jadikan lokasi ini <b>aktif</b>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info rounded-4 mb-0">
                                    <div class="small">
                                        Tips: Ambil koordinat dari Google Maps (klik kanan lokasi kampus → “What’s here?”).
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary rounded-4" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary rounded-4" type="submit">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: Edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formEdit">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lokasi</label>
                                <input id="edit_nama" name="nama_lokasi" class="form-control rounded-4" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Latitude</label>
                                <input id="edit_lat" name="latitude" class="form-control rounded-4" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Longitude</label>
                                <input id="edit_lng" name="longitude" class="form-control rounded-4" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Radius (meter)</label>
                                <input id="edit_radius" name="radius_meter" type="number" min="10" max="5000"
                                       class="form-control rounded-4" required>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_active" value="1">
                                    <label class="form-check-label" for="edit_active">
                                        Jadikan lokasi ini <b>aktif</b>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary rounded-4" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary rounded-4" type="submit">
                            <i class="bi bi-save me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: Delete --}}
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="formDelete">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Hapus Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">Yakin ingin menghapus lokasi berikut?</div>
                        <div class="p-3 bg-light rounded-4">
                            <div class="fw-semibold" id="del_name">-</div>
                        </div>
                        <div class="small text-muted mt-2">Data yang sudah dihapus tidak bisa dikembalikan.</div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary rounded-4" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-danger rounded-4" type="submit">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openEditModal(l) {
                const url = `{{ url('admin/lokasi') }}/${l.id}`;
                document.getElementById('formEdit').action = url;

                document.getElementById('edit_nama').value   = l.nama_lokasi ?? '';
                document.getElementById('edit_lat').value    = l.latitude ?? '';
                document.getElementById('edit_lng').value    = l.longitude ?? '';
                document.getElementById('edit_radius').value = l.radius_meter ?? 200;

                document.getElementById('edit_active').checked = !!l.is_active;

                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            }

            function openDeleteModal(id, name) {
                const url = `{{ url('admin/lokasi') }}/${id}`;
                document.getElementById('formDelete').action = url;

                document.getElementById('del_name').innerText = name;

                new bootstrap.Modal(document.getElementById('modalDelete')).show();
            }
        </script>
    @endpush
</x-admin-layout>