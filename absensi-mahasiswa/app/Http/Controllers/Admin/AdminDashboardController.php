<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Total mahasiswa
        $totalMahasiswa = (int) DB::table('mahasiswa')->count();

        // Hadir hari ini = mahasiswa yang sudah IN hari ini (distinct mahasiswa_id)
        $hadirHariIni = (int) DB::table('absensi')
            ->whereDate('tanggal', $today)
            ->where('type', 'in')
            ->distinct('mahasiswa_id')
            ->count('mahasiswa_id');

        // Belum absen = total - hadir (IN)
        $belumAbsen = max(0, $totalMahasiswa - $hadirHariIni);

        // =============================
        // Chart 7 hari terakhir
        // hadir = distinct mahasiswa yang IN per hari
        // belum = total - hadir
        // =============================
        $chartRows = DB::table('absensi')
            ->selectRaw("DATE(tanggal) as tgl,
                COUNT(DISTINCT CASE WHEN type='in' THEN mahasiswa_id END) as hadir")
            ->whereDate('tanggal', '>=', now()->subDays(6)->toDateString())
            ->groupByRaw("DATE(tanggal)")
            ->orderBy('tgl', 'asc')
            ->get();

        $labels = [];
        $dataHadir = [];
        $dataBelum = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->toDateString();
            $labels[] = $i === 0 ? 'Hari ini' : 'H-' . $i;

            $hadirTgl = (int) (optional($chartRows->firstWhere('tgl', $tgl))->hadir ?? 0);
            $belumTgl = max(0, $totalMahasiswa - $hadirTgl);

            $dataHadir[] = $hadirTgl;
            $dataBelum[] = $belumTgl;
        }

        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'hadirHariIni',
            'belumAbsen',
            'labels',
            'dataHadir',
            'dataBelum'
        ));
    }
}
