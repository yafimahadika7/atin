{{-- resources/views/admin/dashboard.blade.php --}}

<x-admin-layout :title="'Dashboard Admin'">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Admin</h4>
            <div class="text-muted small">Monitoring ringkas absensi & lokasi UNPAM</div>
        </div>

        <button class="btn btn-primary rounded-4" type="button"
                onclick="showGlobalModal('Info', '<div class=\'small text-muted\'>Data dashboard sudah realtime dari database.</div>')">
            <i class="bi bi-info-circle me-1"></i> Info
        </button>
    </div>

    {{-- Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-soft">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-people-fill fs-1 text-primary"></i>
                    <div>
                        <small class="text-muted">Total Mahasiswa</small>
                        <h4 class="fw-bold mb-0">{{ $totalMahasiswa }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-soft">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-camera-fill fs-1 text-success"></i>
                    <div>
                        <small class="text-muted">Hadir Hari Ini (IN)</small>
                        <h4 class="fw-bold mb-0">{{ $hadirHariIni }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-soft">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-exclamation-circle fs-1 text-danger"></i>
                    <div>
                        <small class="text-muted">Belum Absen</small>
                        <h4 class="fw-bold mb-0">{{ $belumAbsen }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-4 align-items-stretch">
        <div class="col-md-6 d-flex">
            <div class="card shadow-soft w-100">
                <div class="card-header bg-white fw-semibold">
                    Kondisi Absensi Hari Ini
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="chartToday" style="max-height:280px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 d-flex">
            <div class="card shadow-soft w-100">
                <div class="card-header bg-white fw-semibold">
                    Tren Absensi 7 Hari Terakhir
                </div>
                <div class="card-body">
                    <canvas id="chartWeekly" style="max-height:280px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Hari ini
            const hadir = {{ (int)$hadirHariIni }};
            const belum = {{ (int)$belumAbsen }};

            new Chart(document.getElementById('chartToday'), {
                type: 'doughnut',
                data: {
                    labels: ['Hadir (IN)', 'Belum Absen'],
                    datasets: [{ data: [hadir, belum] }]
                }
            });

            // 7 hari terakhir (realtime)
            const labels = @json($labels);
            const dataHadir = @json($dataHadir);
            const dataBelum = @json($dataBelum);

            new Chart(document.getElementById('chartWeekly'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        { label: 'Hadir (IN)', data: dataHadir, tension: 0.3 },
                        { label: 'Belum',      data: dataBelum, tension: 0.3 },
                    ]
                }
            });
        </script>
    @endpush
</x-admin-layout>
