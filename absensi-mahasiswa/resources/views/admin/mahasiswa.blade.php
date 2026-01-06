{{-- resources/views/admin/mahasiswa.blade.php --}}

<x-admin-layout title="Data Mahasiswa">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Data Mahasiswa</h4>
            <div class="text-muted small">CRUD data mahasiswa (NIM, Prodi, Kelas)</div>
        </div>

        <button class="btn btn-primary rounded-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mahasiswa
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

    {{-- Search --}}
    <div class="card shadow-soft mb-3">
        <div class="card-body">
            <form class="row g-2" method="GET" action="{{ route('admin.mahasiswa.index') }}">
                <div class="col-md-10">
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control rounded-4"
                           placeholder="Cari NIM / Nama / Prodi / Kelas...">
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
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Prodi</th>
                            <th>Kelas</th>
                            <th style="width:160px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswa as $i => $m)
                            <tr>
                                <td>{{ $mahasiswa->firstItem() + $i }}</td>
                                <td class="fw-semibold">{{ $m->nim }}</td>
                                <td>{{ $m->nama ?? '-' }}</td>
                                <td>{{ $m->prodi ?? '-' }}</td>
                                <td>{{ $m->kelas ?? '-' }}</td>
                                <td class="text-end">
                                    <button
                                        class="btn btn-sm btn-outline-primary rounded-4"
                                        type="button"
                                        onclick='openEditModal(@json($m))'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button
                                        class="btn btn-sm btn-outline-danger rounded-4"
                                        type="button"
                                        onclick="openDeleteModal({{ $m->id }}, @json($m->nama ?? '-'), @json($m->nim))">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada data mahasiswa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $mahasiswa->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL: Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.mahasiswa.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Mahasiswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">NIM</label>
                                <input name="nim" class="form-control rounded-4" placeholder="contoh: 221011400189" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama</label>
                                <input name="nama" class="form-control rounded-4" placeholder="Nama mahasiswa">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prodi</label>
                                <input name="prodi" class="form-control rounded-4" placeholder="Teknik Informatika">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>
                                <input name="kelas" class="form-control rounded-4" placeholder="01">
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info rounded-4 mb-0">
                                    <div class="small">
                                        Password otomatis: <b>#unpam + 6 digit terakhir NIM</b>.
                                        Akun login mahasiswa dibuat otomatis.
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
                        <h5 class="modal-title">Edit Mahasiswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">NIM</label>
                                <input id="edit_nim" name="nim" class="form-control rounded-4" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama</label>
                                <input id="edit_nama" name="nama" class="form-control rounded-4">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prodi</label>
                                <input id="edit_prodi" name="prodi" class="form-control rounded-4">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>
                                <input id="edit_kelas" name="kelas" class="form-control rounded-4">
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
                        <h5 class="modal-title text-danger">Hapus Mahasiswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">Yakin ingin menghapus data berikut?</div>
                        <div class="p-3 bg-light rounded-4">
                            <div class="fw-semibold" id="del_name">-</div>
                            <div class="small text-muted" id="del_nim">-</div>
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
            function openEditModal(m) {
                const url = `{{ url('admin/mahasiswa') }}/${m.id}`;
                document.getElementById('formEdit').action = url;

                document.getElementById('edit_nim').value   = m.nim ?? '';
                document.getElementById('edit_nama').value  = m.nama ?? '';
                document.getElementById('edit_prodi').value = m.prodi ?? '';
                document.getElementById('edit_kelas').value = m.kelas ?? '';

                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            }

            function openDeleteModal(id, name, nim) {
                const url = `{{ url('admin/mahasiswa') }}/${id}`;
                document.getElementById('formDelete').action = url;

                document.getElementById('del_name').innerText = name;
                document.getElementById('del_nim').innerText  = nim;

                new bootstrap.Modal(document.getElementById('modalDelete')).show();
            }
        </script>
    @endpush
</x-admin-layout>