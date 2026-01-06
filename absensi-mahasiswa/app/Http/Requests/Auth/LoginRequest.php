<?php

namespace App\Http\Requests\Auth;

use App\Models\Mahasiswa;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules:
     * - Kolom "email" kita jadikan "username" (boleh email atau NIM)
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'], // boleh email ATAU NIM
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * Login flow:
     * 1) Coba login normal via email (admin/dosen/mahasiswa yang pakai email)
     * 2) Kalau gagal dan inputnya terlihat seperti NIM, cari di tabel mahasiswa -> ambil user -> attempt pakai email user tsb
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = trim((string) $this->input('email')); // bisa email atau NIM
        $password = (string) $this->input('password');
        $remember = $this->boolean('remember');

        // 1) Coba login normal sebagai EMAIL
        if (Auth::attempt(['email' => $loginInput, 'password' => $password], $remember)) {
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // 2) Kalau bukan email, anggap NIM (atau kalau email salah format)
        //    Cari data mahasiswa berdasarkan nim, lalu login menggunakan email user terkait.
        $mahasiswa = Mahasiswa::where('nim', $loginInput)->first();

        if ($mahasiswa && $mahasiswa->user) {
            // optional: pastikan memang role mahasiswa
            if ($mahasiswa->user->role !== 'mahasiswa') {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }

            if (Auth::attempt(['email' => $mahasiswa->user->email, 'password' => $password], $remember)) {
                RateLimiter::clear($this->throttleKey());
                return;
            }
        }

        // Kalau semua gagal
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        // pakai input "email" sebagai key (email/NIM)
        return Str::transliterate(Str::lower((string) $this->input('email')) . '|' . $this->ip());
    }
}
