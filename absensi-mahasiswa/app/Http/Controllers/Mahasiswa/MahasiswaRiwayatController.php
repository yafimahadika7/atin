<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class MahasiswaRiwayatController extends Controller
{
    private function currentMahasiswa(): Mahasiswa
    {
        return Mahasiswa::where('user_id', auth()->id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $mhs = $this->currentMahasiswa();

        $dari = $request->query('dari');
        $sampai = $request->query('sampai');

        $q = Absensi::where('mahasiswa_id', $mhs->id);

        if ($dari) {
            $q->whereDate('tanggal', '>=', $dari);
        }
        if ($sampai) {
            $q->whereDate('tanggal', '<=', $sampai);
        }

        // ambil semua record, nanti kita group per tanggal
        $rows = $q->orderByDesc('tanggal')->orderByDesc('jam')->get();

        // group per tanggal
        $grouped = $rows->groupBy(function ($item) {
            return (string) $item->tanggal;
        });

        // bentuk rekap per tanggal
        $rekap = $grouped->map(function ($items, $tanggal) {
            $in = $items->firstWhere('type', 'in');
            $out = $items->firstWhere('type', 'out');

            $jamIn = $in?->jam ? \Carbon\Carbon::parse($in->jam)->timezone('Asia/Jakarta')->format('H:i:s') : '--';
            $jamOut = $out?->jam ? \Carbon\Carbon::parse($out->jam)->timezone('Asia/Jakarta')->format('H:i:s') : '--';

            $status = 'Belum Absen';
            if ($in && !$out) $status = 'Menunggu OUT';
            if ($in && $out) $status = 'Lengkap (IN/OUT)';

            return [
                'tanggal' => $tanggal,
                'jam_in' => $jamIn,
                'jam_out' => $jamOut,
                'foto_in' => $in?->foto,
                'foto_out' => $out?->foto,
                'lat_in' => $in?->latitude,
                'lng_in' => $in?->longitude,
                'lat_out' => $out?->latitude,
                'lng_out' => $out?->longitude,
                'status' => $status,
            ];
        })->values(); // biar index rapih

        return view('mahasiswa.riwayat', compact('rekap', 'dari', 'sampai'));
    }
}