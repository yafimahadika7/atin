<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mahasiswa') | Absensi Mahasiswa UNPAM</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background:#f4f6f9; }
        .wrapper { display:flex; min-height:100vh; }

        /* Sidebar */
        #sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0b1220, #0f172a, #0b1220);
            color: #fff;
            padding: 1.2rem;
            transition: margin-left .3s ease;
        }
        #sidebar.collapsed { margin-left: -260px; }

        .sidebar-brand {
            font-size: 1.05rem;
            font-weight: 600;
            letter-spacing: .7px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .sidebar-brand i { font-size: 1.9rem; color:#38bdf8; }

        #sidebar .nav-link {
            color: #cbd5f5;
            border-radius: 12px;
            padding: .75rem 1rem;
            margin-bottom: .45rem;
            display:flex;
            align-items:center;
            gap:.7rem;
            transition:.25s;
        }
        #sidebar .nav-link i { font-size: 1.15rem; color:#38bdf8; }
        #sidebar .nav-link.active,
        #sidebar .nav-link:hover {
            background: rgba(56,189,248,.15);
            color:#fff;
        }
        #sidebar .nav-link.active i,
        #sidebar .nav-link:hover i { color:#fff; }

        /* Topbar */
        .topbar {
            background: rgba(11,18,32,.95);
            backdrop-filter: blur(8px);
        }
        .topbar .navbar-brand { font-weight:600; letter-spacing:.5px; }

        /* Content */
        #main-content { flex:1; padding: 1.8rem; }

        /* Nice cards */
        .shadow-soft { box-shadow: 0 10px 25px rgba(2,6,23,.08); border:0; border-radius: 18px; }
        .rounded-4 { border-radius: 16px !important; }

        /* Logout button */
        .btn-logout { background:#ef4444; border:none; border-radius: 14px; }
        .btn-logout:hover { background:#dc2626; }

        @media (max-width: 768px) {
            #sidebar {
                position: fixed;
                top: 56px;
                left: 0;
                height: calc(100vh - 56px);
                z-index: 1050;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<nav class="navbar navbar-dark topbar">
    <div class="container-fluid">
        <button class="btn btn-outline-light" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <span class="navbar-brand ms-3">
            ABSENSI MAHASISWA UNPAM
        </span>

        <span class="text-light ms-auto small">
            <i class="bi bi-person-circle me-1"></i>
            {{ auth()->user()->name }} (Mahasiswa)
        </span>
    </div>
</nav>

<div class="wrapper">
    <aside id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-camera-fill"></i><br>
            PANEL MAHASISWA
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('mahasiswa.dashboard') }}"
                class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('mahasiswa.riwayat') }}"
                class="nav-link {{ request()->routeIs('mahasiswa.riwayat') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    Riwayat Absensi
                </a>
            </li>
        </ul>

        <hr class="text-secondary">

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-logout w-100 text-white">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </aside>

    <main id="main-content">
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle').onclick = () =>
        document.getElementById('sidebar').classList.toggle('collapsed');
</script>

@stack('scripts')
</body>
</html>
