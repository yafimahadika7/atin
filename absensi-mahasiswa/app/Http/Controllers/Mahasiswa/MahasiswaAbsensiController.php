<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class MahasiswaAbsensiController extends Controller
{
    private function currentMahasiswa(): Mahasiswa
    {
        // mahasiswa.user_id = users.id (sesuai tabel Anda)
        return Mahasiswa::where('user_id', auth()->id())->firstOrFail();
    }

    public function showIn()
    {
        $mhs = $this->currentMahasiswa();
        $today = now()->toDateString();

        $sudahIn = Absensi::where('mahasiswa_id', $mhs->id)
            ->whereDate('tanggal', $today)
            ->where('type', 'in')
            ->exists();

        return view('mahasiswa.absen_in', compact('mhs', 'sudahIn'));
    }

    public function storeIn(Request $request)
    {
        $mhs = $this->currentMahasiswa();
        $today = now()->toDateString();

        // blok kalau sudah IN
        $sudahIn = Absensi::where('mahasiswa_id', $mhs->id)
            ->whereDate('tanggal', $today)
            ->where('type', 'in')
            ->exists();

        if ($sudahIn) {
            return back()->with('error', 'Anda sudah melakukan Absen Masuk (IN) hari ini.');
        }

        $data = $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $path = $request->file('foto')->store('absensi', 'public');

        Absensi::create([
            'mahasiswa_id' => $mhs->id,
            'tanggal' => $today,
            'type' => 'in',
            'jam' => now()->format('H:i:s'),
            'foto_path' => $path,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Absen Masuk (IN) berhasil.');
    }

    public function showOut()
    {
        $mhs = $this->currentMahasiswa();
        $today = now()->toDateString();

        $sudahIn = Absensi::where('mahasiswa_id', $mhs->id)
            ->whereDate('tanggal', $today)
            ->where('type', 'in')
            ->exists();

        $sudahOut = Absensi::where('mahasiswa_id', $mhs->id)
            ->whereDate('tanggal', $today)
            ->where('type', 'out')
            ->exists();

        return view('mahasiswa.absen_out', compact('mhs', 'sudahIn', 'sudahOut'));
    }

    public function storeOut(Request $request)
    {
        $mhs = $this->currentMahasiswa();
        $today = now()->toDateString();

        $sudahIn = Absensi::where('mahasiswa_id', $mhs->id)
            ->whereDate('tanggal', $today)
            ->where('type', 'in')
            ->exists();

        if (!$sudahIn) {
            return back()->with('error', 'Anda belum Absen Masuk (IN). Silakan absen IN dulu.');
        }

        $sudahOut = Absensi::where('mahasiswa_id', $mhs->id)
            ->whereDate('tanggal', $today)
            ->where('type', 'out')
            ->exists();

        if ($sudahOut) {
            return back()->with('error', 'Anda sudah melakukan Absen Pulang (OUT) hari ini.');
        }

        $data = $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $path = $request->file('foto')->store('absensi', 'public');

        Absensi::create([
            'mahasiswa_id' => $mhs->id,
            'tanggal' => $today,
            'type' => 'out',
            'jam' => now()->format('H:i:s'),
            'foto_path' => $path,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Absen Pulang (OUT) berhasil.');
    }

    public function riwayat(Request $request)
    {
        $mhs = $this->currentMahasiswa();

        $riwayat = Absensi::where('mahasiswa_id', $mhs->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('jam')
            ->paginate(10);

        return view('mahasiswa.riwayat', compact('mhs', 'riwayat'));
    }

    public function dashboard()
    {
        $mhs = $this->currentMahasiswa();
        $today = now()->toDateString();

        $absenIn = \App\Models\Absensi::where('mahasiswa_id', $mhs->id)
            ->whereDate('tanggal', $today)
            ->where('type', 'in')
            ->first();

        $absenOut = \App\Models\Absensi::where('mahasiswa_id', $mhs->id)
            ->whereDate('tanggal', $today)
            ->where('type', 'out')
            ->first();

        $statusHariIni = 'Belum Absen';
        $jamMasuk = '--';
        $jamPulang = '--';

        if ($absenIn) {
            $statusHariIni = 'Sudah Absen Masuk';
            $jamMasuk = $absenIn->jam;
        }
        if ($absenOut) {
            $statusHariIni = 'Sudah Absen Pulang';
            $jamPulang = $absenOut->jam;
        }
        if ($absenIn && !$absenOut) {
            $statusHariIni = 'Menunggu Absen Pulang';
        }

        return view('mahasiswa.dashboard', compact(
            'statusHariIni','jamMasuk','jamPulang','absenIn','absenOut'
        ));
    }
}