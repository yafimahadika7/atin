{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Absensi Mahasiswa UNPAM</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* biar mirip contoh: background biru + foto kampus */
        .bg-login {
            background-image:
                linear-gradient(rgba(37, 99, 235, .82), rgba(37, 99, 235, .82)),
                url('{{ asset('images/unpam-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="min-h-screen bg-login flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-5xl">
        <div class="bg-white/95 backdrop-blur rounded-2xl shadow-2xl overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2">
                {{-- KIRI: Panel Info --}}
                <div class="bg-blue-700 text-white p-10 flex flex-col justify-center">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center shadow">
                            <img
                                src="{{ asset('images/unpam-logo.png') }}"
                                alt="Logo UNPAM"
                                class="w-12 h-12 object-contain"
                                onerror="this.style.display='none'"
                            >
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold leading-tight">Sistem Absensi</h1>
                            <p class="text-white/80 -mt-0.5">Mahasiswa UNPAM</p>
                        </div>
                    </div>

                    <div class="mt-8 space-y-3 text-white/85">
                        <p class="text-lg font-semibold">Sistem Informasi Absensi Mahasiswa</p>
                        <p class="text-sm leading-relaxed">
                            Silakan login menggunakan akun yang sudah terdaftar.
                            Absensi perkuliahan dilakukan secara terstruktur dan tercatat otomatis.
                        </p>

                        <div class="pt-4 text-xs text-white/70">
                            © {{ date('Y') }} UNPAM • Absensi Mahasiswa
                        </div>
                    </div>
                </div>

                {{-- KANAN: Form Login --}}
                <div class="p-10">
                    <div class="text-center">
                        <h2 class="text-xl font-bold text-slate-800 tracking-wide">LOGIN</h2>
                        <p class="text-sm text-slate-500 mt-1">Masuk ke sistem absensi</p>
                    </div>

                    {{-- status / error bawaan breeze --}}
                    @if (session('status'))
                        <div class="mt-6 rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-6 rounded-lg bg-red-50 text-red-700 px-4 py-3 text-sm">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                        @csrf

                        {{-- Username / Email --}}
                        <div>
                            <label class="text-sm font-medium text-slate-700">Username</label>
                            <input
                                type="text"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Username / Email"
                                required
                                autofocus
                                class="mt-2 w-full rounded-full border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                            >
                            <p class="text-xs text-slate-400 mt-2">
                                *Jika sistem Anda pakai NIM/username, nanti kita sesuaikan kolomnya.
                            </p>
                        </div>

                        {{-- Password + tombol Lihat --}}
                        <div>
                            <label class="text-sm font-medium text-slate-700">Password</label>

                            <div class="mt-2 flex items-stretch">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Password"
                                    required
                                    class="w-full rounded-l-full border border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                                >
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="rounded-r-full border border-l-0 border-slate-200 px-5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                >
                                    Lihat
                                </button>
                            </div>
                        </div>

                        {{-- Remember + Forgot --}}
                        <div class="flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                >
                                Ingat saya
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-blue-700 hover:underline">
                                    Lupa password?
                                </a>
                            @endif
                        </div>

                        {{-- Button Masuk --}}
                        <button
                            type="submit"
                            class="w-full rounded-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 shadow-lg shadow-blue-600/30 transition"
                        >
                            Masuk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // tombol "Lihat" password
        const btn = document.getElementById('togglePassword');
        const input = document.getElementById('password');

        btn?.addEventListener('click', () => {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.textContent = isPassword ? 'Sembunyi' : 'Lihat';
        });
    </script>
</body>
</html>
