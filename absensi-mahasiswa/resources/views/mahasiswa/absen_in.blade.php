@extends('mahasiswa.layout')

@section('title', 'Absen Masuk (IN)')

@section('content')
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Absen Masuk (IN)</h4>
        <div class="text-muted small">Ambil foto dan lokasi, lalu kirim absensi masuk.</div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-soft">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-4 shadow-soft">{{ session('error') }}</div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-soft">
            <b>Gagal:</b>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-soft">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="fw-semibold">Form Absen Masuk</div>
                            <div class="text-muted small">Tanggal: {{ now()->format('Y-m-d') }}</div>
                        </div>
                        @if($sudahIn ?? false)
                            <span class="badge bg-success rounded-pill px-3">SUDAH IN</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3">BELUM IN</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('mahasiswa.absen.in.store') }}" enctype="multipart/form-data" id="formAbsenIn">
                        @csrf

                        {{-- FOTO --}}
                        <div class="mb-3">
                            <label class="form-label">Foto Absensi (wajib)</label>
                            <input type="file" name="foto" accept="image/*" capture="environment"
                                   class="form-control rounded-4" id="fotoInputIn"
                                   {{ ($sudahIn ?? false) ? 'disabled' : '' }} required>
                            <div class="form-text">
                                Gunakan kamera (disarankan). JPG/PNG maksimal 4MB.
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div class="mb-3">
                            <div class="p-3 bg-light rounded-4 text-center">
                                <img id="fotoPreviewIn" src="" alt="Preview Foto"
                                     class="img-fluid rounded-4 d-none" style="max-height: 360px;">
                                <div id="fotoPlaceholderIn" class="text-muted small">
                                    Preview foto akan muncul di sini.
                                </div>
                            </div>
                        </div>

                        {{-- LOKASI --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="latitude" id="latIn" class="form-control rounded-4" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="longitude" id="lngIn" class="form-control rounded-4" readonly>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info rounded-4 mb-0">
                                    <div class="small">
                                        Lokasi diambil otomatis dari browser. Jika tidak muncul, pastikan izin lokasi diaktifkan.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 flex-wrap">
                            @if($sudahIn ?? false)
                                <button type="button" class="btn btn-secondary rounded-4" disabled>
                                    <i class="bi bi-check2-circle me-1"></i> Anda sudah Absen IN hari ini
                                </button>
                            @else
                                <button type="submit" class="btn btn-primary rounded-4" id="btnSubmitIn">
                                    <i class="bi bi-send me-1"></i> Kirim Absen IN
                                </button>
                                <button type="button" class="btn btn-outline-dark rounded-4" id="btnGetLocIn">
                                    <i class="bi bi-geo-alt me-1"></i> Ambil Lokasi
                                </button>
                            @endif

                            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-outline-secondary rounded-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>

                        <div class="small text-muted mt-3" id="locStatusIn">Status lokasi: menunggu...</div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Info box --}}
        <div class="col-lg-5">
            <div class="card shadow-soft">
                <div class="card-body">
                    <div class="fw-semibold mb-2"><i class="bi bi-info-circle me-1"></i> Petunjuk</div>
                    <ol class="small text-muted mb-0">
                        <li>Aktifkan izin lokasi (GPS) di browser.</li>
                        <li>Ambil foto yang jelas (wajah terlihat, pencahayaan cukup).</li>
                        <li>Klik “Kirim Absen IN”.</li>
                        <li>Jika gagal, refresh lalu coba lagi.</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-soft mt-4">
                <div class="card-body">
                    <div class="fw-semibold mb-2"><i class="bi bi-shield-check me-1"></i> Catatan</div>
                    <div class="small text-muted">
                        Data absensi tersimpan dengan waktu server. Pastikan jaringan stabil saat upload.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Preview foto
    const fotoInputIn = document.getElementById('fotoInputIn');
    const fotoPreviewIn = document.getElementById('fotoPreviewIn');
    const fotoPlaceholderIn = document.getElementById('fotoPlaceholderIn');

    if (fotoInputIn) {
        fotoInputIn.addEventListener('change', function () {
            const file = this.files?.[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            fotoPreviewIn.src = url;
            fotoPreviewIn.classList.remove('d-none');
            fotoPlaceholderIn.classList.add('d-none');
        });
    }

    // Ambil lokasi
    const latIn = document.getElementById('latIn');
    const lngIn = document.getElementById('lngIn');
    const locStatusIn = document.getElementById('locStatusIn');
    const btnGetLocIn = document.getElementById('btnGetLocIn');

    function setStatusIn(text) {
        if (locStatusIn) locStatusIn.innerText = text;
    }

    function getLocationIn() {
        if (!navigator.geolocation) {
            setStatusIn('Status lokasi: Browser tidak mendukung geolocation.');
            return;
        }

        setStatusIn('Status lokasi: mengambil lokasi...');

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                latIn.value = pos.coords.latitude;
                lngIn.value = pos.coords.longitude;
                setStatusIn('Status lokasi: lokasi berhasil diambil ✅');
            },
            (err) => {
                setStatusIn('Status lokasi: gagal ambil lokasi ❌ (' + err.message + ')');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    if (btnGetLocIn) {
        btnGetLocIn.addEventListener('click', getLocationIn);
        // auto ambil lokasi saat halaman dibuka
        getLocationIn();
    }

    // Disable tombol saat submit
    const formAbsenIn = document.getElementById('formAbsenIn');
    const btnSubmitIn = document.getElementById('btnSubmitIn');

    if (formAbsenIn && btnSubmitIn) {
        formAbsenIn.addEventListener('submit', function () {
            btnSubmitIn.disabled = true;
            btnSubmitIn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
        });
    }
</script>
@endpush
