    {{-- resources/views/admin/layout.blade.php --}}
    <!doctype html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Admin Panel' }} | Absensi Mahasiswa UNPAM</title>

        {{-- Bootstrap --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        {{-- Bootstrap Icons --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        {{-- Google Font --}}
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

        <style>
            body { font-family: 'Poppins', sans-serif; background: #f4f6f9; }

            .wrapper { display:flex; min-height:100vh; }

            /* Sidebar */
            #sidebar {
                width: 260px;
                background: linear-gradient(180deg, #020617, #0f172a, #020617);
                color: #fff;
                padding: 1.2rem;
                transition: margin-left .25s ease;
            }
            #sidebar.collapsed { margin-left:-260px; }

            .sidebar-brand {
                font-size: 1.05rem;
                font-weight: 600;
                letter-spacing: .8px;
                margin-bottom: 1.25rem;
                text-align: center;
            }
            .sidebar-brand i { font-size: 1.8rem; color: #38bdf8; }

            #sidebar .nav-link {
                color: #cbd5f5;
                border-radius: 12px;
                padding: .75rem 1rem;
                margin-bottom: .45rem;
                display:flex; align-items:center; gap:.7rem;
                transition: .2s;
            }
            #sidebar .nav-link i { font-size: 1.1rem; color:#38bdf8; }
            #sidebar .nav-link.active, #sidebar .nav-link:hover {
                background: rgba(56,189,248,.15);
                color:#fff;
            }
            #sidebar .nav-link.active i, #sidebar .nav-link:hover i { color:#fff; }

            /* Topbar */
            .topbar { background: rgba(2,6,23,.95); backdrop-filter: blur(8px); }
            .topbar .navbar-brand { font-weight: 600; letter-spacing: .5px; }

            /* Main */
            #main-content { flex:1; padding: 1.8rem; }

            /* Logout */
            .btn-logout { background:#ef4444; border:none; border-radius: 12px; }
            .btn-logout:hover { background:#dc2626; }

            /* Card look */
            .card { border:0; border-radius: 16px; }
            .card.shadow-soft { box-shadow: 0 10px 30px rgba(15,23,42,.08); }

            /* Modal */
            .modal-content { background:#fff !important; border-radius: 16px; }
            .modal-backdrop.show { opacity:.6; }

            @media (max-width: 768px) {
                #sidebar {
                    position: fixed;
                    top: 56px; left:0;
                    height: calc(100vh - 56px);
                    z-index: 1050;
                }
            }
        </style>

        @stack('styles')
    </head>

    <body>
        {{-- Topbar --}}
        <nav class="navbar navbar-dark topbar">
            <div class="container-fluid">
                <button class="btn btn-outline-light" id="sidebarToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>

                <span class="navbar-brand ms-3">
                    ABSENSI MAHASISWA UNPAM
                </span>

                <span class="text-light ms-auto small">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
                </span>
            </div>
        </nav>

        <div class="wrapper">
            {{-- Sidebar --}}
            <aside id="sidebar">
                <div class="sidebar-brand">
                    <i class="bi bi-fingerprint"></i><br>
                    ADMIN PANEL
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.absensi.index') }}"
                        class="nav-link {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check"></i>
                            Data Absensi
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.mahasiswa.index') }}"
                        class="nav-link {{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}">
                            <i class="bi bi-mortarboard-fill"></i>
                            Data Mahasiswa
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.lokasi.index') }}"
                        class="nav-link {{ request()->routeIs('admin.lokasi.*') ? 'active' : '' }}">
                            <i class="bi bi-geo-alt-fill"></i>
                            Lokasi UNPAM
                        </a>
                    </li>
                </ul>

                <hr class="text-secondary">

                {{-- Logout wajib POST --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-logout w-100 text-white" type="submit">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </aside>

            {{-- Main --}}
            <main id="main-content">
                {{-- flash message --}}
                @if (session('success'))
                    <div class="alert alert-success rounded-4 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger rounded-4 shadow-sm">
                        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        {{-- Global modal (opsional dipakai untuk konfirmasi/preview) --}}
        <div class="modal fade" id="globalModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="globalModalTitle">Info</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="globalModalBody">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-4" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- JS --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            // toggle sidebar
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            toggleBtn?.addEventListener('click', () => sidebar.classList.toggle('collapsed'));

            // helper global modal
            window.showGlobalModal = function(title, html) {
                document.getElementById('globalModalTitle').innerText = title || 'Info';
                document.getElementById('globalModalBody').innerHTML = html || '';
                const modal = new bootstrap.Modal(document.getElementById('globalModal'));
                modal.show();
            }
        </script>

        @stack('scripts')
    </body>
    </html>
