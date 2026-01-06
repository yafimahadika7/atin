@extends('mahasiswa.layout')

@section('title', 'Absen Pulang (OUT)')

@section('content')
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Absen Pulang (OUT)</h4>
        <div class="text-muted small">Ambil foto dan lokasi, lalu kirim absensi pulang.</div>
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

    @php
        $sudahIn = $sudahIn ?? false;
        $sudahOut = $sudahOut ?? false;
        $blocked = (!$sudahIn) || $sudahOut;
    @endphp

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-soft">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="fw-semibold">Form Absen Pulang</div>
                            <div class="text-muted small">Tanggal: {{ now()->format('Y-m-d') }}</div>
                        </div>

                        @if(!$sudahIn)
                            <span class="badge bg-danger rounded-pill px-3">BELUM IN</span>
                        @elseif($sudahOut)
                            <span class="badge bg-success rounded-pill px-3">SUDAH OUT</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3">SIAP OUT</span>
                        @endif
                    </div>

                    @if(!$sudahIn)
                        <div class="alert alert-warning rounded-4">
                            Anda <b>belum melakukan Absen Masuk (IN)</b>. Silakan Absen IN dulu.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('mahasiswa.absen.out.store') }}" enctype="multipart/form-data" id="formAbsenOut">
                        @csrf

                        {{-- FOTO --}}
                        <div class="mb-3">
                            <label class="form-label">Foto Absensi Pulang (wajib)</label>
                            <input type="file" name="foto" accept="image/*" capture="environment"
                                   class="form-control rounded-4" id="fotoInputOut"
                                   {{ $blocked ? 'disabled' : '' }} required>
                            <div class="form-text">
                                JPG/PNG maksimal 4MB.
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div class="mb-3">
                            <div class="p-3 bg-light rounded-4 text-center">
                                <img id="fotoPreviewOut" src="" alt="Preview Foto"
                                     class="img-fluid rounded-4 d-none" style="max-height: 360px;">
                                <div id="fotoPlaceholderOut" class="text-muted small">
                                    Preview foto akan muncul di sini.
                                </div>
                            </div>
                        </div>

                        {{-- LOKASI --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="latitude" id="latOut" class="form-control rounded-4" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="longitude" id="lngOut" class="form-control rounded-4" readonly>
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
                            @if($sudahOut)
                                <button type="button" class="btn btn-secondary rounded-4" disabled>
                                    <i class="bi bi-check2-circle me-1"></i> Anda sudah Absen OUT hari ini
                                </button>
                            @elseif(!$sudahIn)
                                <a href="{{ route('mahasiswa.absen.in') }}" class="btn btn-primary rounded-4">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Ke Absen IN
                                </a>
                            @else
                                <button type="submit" class="btn btn-success rounded-4" id="btnSubmitOut">
                                    <i class="bi bi-send me-1"></i> Kirim Absen OUT
                                </button>
                                <button type="button" class="btn btn-outline-dark rounded-4" id="btnGetLocOut">
                                    <i class="bi bi-geo-alt me-1"></i> Ambil Lokasi
                                </button>
                            @endif

                            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-outline-secondary rounded-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>

                        <div class="small text-muted mt-3" id="locStatusOut">Status lokasi: menunggu...</div>
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
                        <li>Pastikan sudah Absen IN terlebih dahulu.</li>
                        <li>Aktifkan izin lokasi (GPS) di browser.</li>
                        <li>Ambil foto pulang (wajah terlihat jelas).</li>
                        <li>Klik “Kirim Absen OUT”.</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-soft mt-4">
                <div class="card-body">
                    <div class="fw-semibold mb-2"><i class="bi bi-shield-check me-1"></i> Catatan</div>
                    <div class="small text-muted">
                        Jika tombol OUT tidak aktif, berarti belum IN atau sudah OUT hari ini.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Preview foto
    const fotoInputOut = document.getElementById('fotoInputOut');
    const fotoPreviewOut = document.getElementById('fotoPreviewOut');
    const fotoPlaceholderOut = document.getElementById('fotoPlaceholderOut');

    if (fotoInputOut) {
        fotoInputOut.addEventListener('change', function () {
            const file = this.files?.[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            fotoPreviewOut.src = url;
            fotoPreviewOut.classList.remove('d-none');
            fotoPlaceholderOut.classList.add('d-none');
        });
    }

    // Ambil lokasi
    const latOut = document.getElementById('latOut');
    const lngOut = document.getElementById('lngOut');
    const locStatusOut = document.getElementById('locStatusOut');
    const btnGetLocOut = document.getElementById('btnGetLocOut');

    function setStatusOut(text) {
        if (locStatusOut) locStatusOut.innerText = text;
    }

    function getLocationOut() {
        if (!navigator.geolocation) {
            setStatusOut('Status lokasi: Browser tidak mendukung geolocation.');
            return;
        }

        setStatusOut('Status lokasi: mengambil lokasi...');

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                latOut.value = pos.coords.latitude;
                lngOut.value = pos.coords.longitude;
                setStatusOut('Status lokasi: lokasi berhasil diambil ✅');
            },
            (err) => {
                setStatusOut('Status lokasi: gagal ambil lokasi ❌ (' + err.message + ')');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    if (btnGetLocOut) {
        btnGetLocOut.addEventListener('click', getLocationOut);
        // auto ambil lokasi saat halaman dibuka
        getLocationOut();
    }

    // Disable tombol saat submit
    const formAbsenOut = document.getElementById('formAbsenOut');
    const btnSubmitOut = document.getElementById('btnSubmitOut');

    if (formAbsenOut && btnSubmitOut) {
        formAbsenOut.addEventListener('submit', function () {
            btnSubmitOut.disabled = true;
            btnSubmitOut.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
        });
    }
</script>
@endpush
