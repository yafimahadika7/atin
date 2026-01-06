<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim((string) $request->query('q'));
        $date = $request->query('date'); // YYYY-MM-DD (optional)

        // Ambil absensi + mahasiswa, lalu rekap per (mahasiswa_id + tanggal)
        $rows = Absensi::query()
            ->with(['mahasiswa'])
            ->when($date, fn($qr) => $qr->whereDate('tanggal', $date))
            ->when($q, function ($qr) use ($q) {
                $qr->whereHas('mahasiswa', function ($m) use ($q) {
                    $m->where('nim', 'like', "%{$q}%")
                      ->orWhere('nama', 'like', "%{$q}%")
                      ->orWhere('prodi', 'like', "%{$q}%")
                      ->orWhere('kelas', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('jam')
            ->get();

        // Rekap di PHP: key = mahasiswa_id|tanggal
        $rekap = [];

        foreach ($rows as $a) {
            $key = $a->mahasiswa_id . '|' . $a->tanggal;

            if (!isset($rekap[$key])) {
                $rekap[$key] = [
                    'tanggal' => $a->tanggal,
                    'mahasiswa' => $a->mahasiswa,
                    'in' => null,
                    'out' => null,
                ];
            }

            if ($a->type === 'in') {
                $rekap[$key]['in'] = $a;
            } elseif ($a->type === 'out') {
                $rekap[$key]['out'] = $a;
            }
        }

        // urutkan rekap (tanggal desc, nim asc)
        $rekap = collect($rekap)
            ->sortBy(function ($item) {
                $nim = $item['mahasiswa']->nim ?? '';
                return $nim;
            })
            ->sortByDesc(function ($item) {
                return $item['tanggal'];
            })
            ->values();

        // pagination manual (simple)
        $perPage = 10;
        $page = max(1, (int) $request->query('page', 1));
        $total = $rekap->count();

        $items = $rekap->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('admin.absensi', [
            'absensi' => $paginator,
            'q' => $q,
            'date' => $date,
        ]);
    }
}