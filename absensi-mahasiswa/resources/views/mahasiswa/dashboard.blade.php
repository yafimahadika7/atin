@extends('mahasiswa.layout')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<style>
    /* =============================
       MODAL ABSENSI
    ============================= */
    .modal-absen { max-width: 420px; }

    /* =============================
       TOMBOL AKSI CEPAT
    ============================= */
    .btn-action {
        min-width: 180px;
        padding: 0.6rem 1.2rem;
        font-weight: 500;
    }

    /* =============================
       MOBILE FIRST
    ============================= */
    @media (max-width: 576px) {
        .modal-absen { max-width: 95%; }
        .card-body h5 { font-size: 1.2rem; }
        .alert { font-size: 0.9rem; }
        .btn-action { width: 100%; padding: 0.9rem; font-size: 1rem; }
        .modal-body { padding: 0.75rem; }
        video { max-height: 240px; object-fit: cover; }
    }

    /* =============================
       DESKTOP
    ============================= */
    @media (min-width: 768px) {
        .btn-action { min-width: 200px; }
    }
</style>

<div class="container-fluid">

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success shadow-soft rounded-4">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-soft rounded-4">
            <i class="bi bi-x-circle me-1"></i> {{ session('error') }}
        </div>
    @endif

    {{-- INFO --}}
    <div class="alert alert-info shadow-soft rounded-4">
        <i class="bi bi-info-circle me-2"></i>
        Selamat datang, <strong>{{ auth()->user()->name }}</strong>.
        Silakan lakukan absensi sesuai jadwal Anda.
    </div>

    {{-- STAT BOX --}}
    <div class="row">
        <div class="col-12 col-md-4">
            <div class="card shadow-soft mb-4">
                <div class="card-body">
                    <h6 class="text-muted">Status Hari Ini</h6>
                    <h5 class="fw-bold text-success">{{ $statusHariIni ?? 'Belum Absen' }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-soft mb-4">
                <div class="card-body">
                    <h6 class="text-muted">Jam Masuk</h6>
                    <h5 class="fw-bold">{{ $jamMasuk ?? '--' }}</h5>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-soft mb-4">
                <div class="card-body">
                    <h6 class="text-muted">Jam Pulang</h6>
                    <h5 class="fw-bold">{{ $jamPulang ?? '--' }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTION --}}
    <div class="card shadow-soft border-0">
        <div class="card-body">
            <h6 class="mb-3">Aksi Cepat</h6>

            <div class="d-flex flex-column flex-md-row gap-2">
                {{-- Absen Masuk: disable kalau sudah IN --}}
                <button type="button"
                        class="btn btn-success btn-action"
                        data-bs-toggle="modal"
                        data-bs-target="#modalAbsenMasuk"
                        @if(!empty($absenIn)) disabled @endif>
                    <i class="bi bi-box-arrow-in-right me-1"></i>
                    Absen Masuk
                </button>

                {{-- Absen Pulang: disable kalau belum IN atau sudah OUT --}}
                <button type="button"
                        class="btn btn-outline-danger btn-action"
                        data-bs-toggle="modal"
                        data-bs-target="#modalAbsenPulang"
                        @if(empty($absenIn) || !empty($absenOut)) disabled @endif>
                    <i class="bi bi-box-arrow-left me-1"></i>
                    Absen Pulang
                </button>
            </div>

            <div class="small text-muted mt-3">
                * Absen Pulang aktif jika sudah Absen Masuk dan belum Absen Pulang.
            </div>
        </div>
    </div>

</div>

{{-- =========================
     MODAL ABSEN MASUK (IN)
========================= --}}
<div class="modal fade" id="modalAbsenMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-absen">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Absen Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-secondary py-2 small mb-2">
                    <i class="bi bi-geo-alt me-1"></i>
                    Lokasi: <span id="lokasiMasukText">Mendeteksi...</span>
                </div>

                <video id="videoMasuk" class="w-100 rounded border" autoplay playsinline
                       style="max-height:300px; object-fit:cover;"></video>

                <canvas id="canvasMasuk" class="d-none"></canvas>

                <input type="hidden" id="latMasuk">
                <input type="hidden" id="lngMasuk">

                <small class="text-muted d-block mt-2">
                    Pastikan wajah terlihat jelas dan izin kamera/lokasi diaktifkan.
                </small>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="btnMasuk" type="button">
                    <i class="bi bi-camera me-1"></i> Ambil & Kirim Absen Masuk
                </button>
            </div>

        </div>
    </div>
</div>

{{-- =========================
     MODAL ABSEN PULANG (OUT)
========================= --}}
<div class="modal fade" id="modalAbsenPulang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-absen">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Absen Pulang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-secondary py-2 small mb-2">
                    <i class="bi bi-geo-alt me-1"></i>
                    Lokasi: <span id="lokasiPulangText">Mendeteksi...</span>
                </div>

                <video id="videoPulang" class="w-100 rounded border" autoplay playsinline
                       style="max-height:300px; object-fit:cover;"></video>

                <canvas id="canvasPulang" class="d-none"></canvas>

                <input type="hidden" id="latPulang">
                <input type="hidden" id="lngPulang">

                <small class="text-muted d-block mt-2">
                    Pastikan wajah terlihat jelas dan izin kamera/lokasi diaktifkan.
                </small>
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger" id="btnPulang" type="button">
                    <i class="bi bi-camera me-1"></i> Ambil & Kirim Absen Pulang
                </button>
            </div>

        </div>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let streamMasuk = null;
    let streamPulang = null;

    const CSRF_TOKEN = @json(csrf_token());
    const URL_IN  = @json(route('mahasiswa.absen.in.store'));
    const URL_OUT = @json(route('mahasiswa.absen.out.store'));

    function startCamera(videoId, tipe) {
        return navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(s => {
                if (tipe === 'masuk') streamMasuk = s;
                if (tipe === 'pulang') streamPulang = s;
                document.getElementById(videoId).srcObject = s;
                return true;
            })
            .catch(() => {
                Swal.fire('Gagal', 'Izin kamera ditolak / tidak tersedia.', 'error');
                return false;
            });
    }

    function stopCamera(tipe) {
        const s = (tipe === 'masuk') ? streamMasuk : streamPulang;
        if (s) s.getTracks().forEach(t => t.stop());
        if (tipe === 'masuk') streamMasuk = null;
        if (tipe === 'pulang') streamPulang = null;
    }

    function getLocation(latInputId, lngInputId, textId) {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject('Browser tidak mendukung geolocation.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                pos => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    document.getElementById(latInputId).value = lat;
                    document.getElementById(lngInputId).value = lng;
                    document.getElementById(textId).innerText = lat.toFixed(6) + ', ' + lng.toFixed(6);
                    resolve({ lat, lng });
                },
                () => reject('Izin lokasi ditolak / gagal mendeteksi lokasi.'),
                { enableHighAccuracy: true, timeout: 15000 }
            );
        });
    }

    function captureToBlob(videoId, canvasId) {
        return new Promise((resolve) => {
            const video = document.getElementById(videoId);
            const canvas = document.getElementById(canvasId);
            const ctx = canvas.getContext('2d');

            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob((blob) => resolve(blob), 'image/jpeg', 0.9);
        });
    }

    async function submitAbsen(tipe) {
        const isMasuk = (tipe === 'masuk');

        const videoId = isMasuk ? 'videoMasuk' : 'videoPulang';
        const canvasId = isMasuk ? 'canvasMasuk' : 'canvasPulang';
        const latId = isMasuk ? 'latMasuk' : 'latPulang';
        const lngId = isMasuk ? 'lngMasuk' : 'lngPulang';
        const btn = isMasuk ? document.getElementById('btnMasuk') : document.getElementById('btnPulang');

        const lat = document.getElementById(latId).value;
        const lng = document.getElementById(lngId).value;

        if (!lat || !lng) {
            Swal.fire('Lokasi belum siap', 'Tunggu lokasi terdeteksi dulu, lalu coba lagi.', 'warning');
            return;
        }

        const blob = await captureToBlob(videoId, canvasId);
        if (!blob) {
            Swal.fire('Gagal', 'Tidak bisa mengambil foto dari kamera.', 'error');
            return;
        }

        const fd = new FormData();
        fd.append('_token', CSRF_TOKEN);
        fd.append('latitude', lat);
        fd.append('longitude', lng);

        // nama input HARUS "foto" agar cocok dengan controller Laravel
        fd.append('foto', blob, isMasuk ? 'absen_in.jpg' : 'absen_out.jpg');

        const url = isMasuk ? URL_IN : URL_OUT;

        btn.disabled = true;

        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await fetch(url, { method: 'POST', body: fd });

            // biasanya controller akan redirect -> fetch akan melihat redirected = true
            if (res.redirected) {
                window.location.href = res.url;
                return;
            }

            if (res.ok) {
                Swal.fire('Berhasil', 'Absensi berhasil.', 'success').then(() => location.reload());
            } else {
                Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan absensi.', 'error');
            }
        } catch (e) {
            Swal.fire('Gagal', 'Tidak bisa terhubung ke server.', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    // Hook modal events (Masuk)
    document.getElementById('modalAbsenMasuk').addEventListener('shown.bs.modal', async () => {
        const camOk = await startCamera('videoMasuk', 'masuk');
        if (camOk) {
            getLocation('latMasuk', 'lngMasuk', 'lokasiMasukText')
                .catch(msg => Swal.fire('Gagal', msg, 'error'));
        }
    });
    document.getElementById('modalAbsenMasuk').addEventListener('hidden.bs.modal', () => stopCamera('masuk'));

    // Hook modal events (Pulang)
    document.getElementById('modalAbsenPulang').addEventListener('shown.bs.modal', async () => {
        const camOk = await startCamera('videoPulang', 'pulang');
        if (camOk) {
            getLocation('latPulang', 'lngPulang', 'lokasiPulangText')
                .catch(msg => Swal.fire('Gagal', msg, 'error'));
        }
    });
    document.getElementById('modalAbsenPulang').addEventListener('hidden.bs.modal', () => stopCamera('pulang'));

    // tombol submit
    document.getElementById('btnMasuk').addEventListener('click', () => submitAbsen('masuk'));
    document.getElementById('btnPulang').addEventListener('click', () => submitAbsen('pulang'));
</script>
@endsection