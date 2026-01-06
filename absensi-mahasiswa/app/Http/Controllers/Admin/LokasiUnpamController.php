<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiUnpam;
use Illuminate\Http\Request;

class LokasiUnpamController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $lokasi = LokasiUnpam::query()
            ->when($q, function ($qr) use ($q) {
                $qr->where('nama_lokasi', 'like', "%{$q}%")
                   ->orWhere('latitude', 'like', "%{$q}%")
                   ->orWhere('longitude', 'like', "%{$q}%");
            })
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.lokasi', compact('lokasi', 'q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lokasi'  => ['required', 'string', 'max:150'],
            'latitude'     => ['required', 'numeric', 'between:-90,90'],
            'longitude'    => ['required', 'numeric', 'between:-180,180'],
            'radius_meter' => ['required', 'integer', 'min:10', 'max:5000'],
            'is_active'    => ['nullable'],
        ]);

        $setActive = $request->boolean('is_active');

        if ($setActive) {
            LokasiUnpam::query()->update(['is_active' => 0]); // hanya 1 aktif
        }

        LokasiUnpam::create([
            'nama_lokasi'  => $data['nama_lokasi'],
            'latitude'     => $data['latitude'],
            'longitude'    => $data['longitude'],
            'radius_meter' => $data['radius_meter'],
            'is_active'    => $setActive ? 1 : 0,
        ]);

        return back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function update(Request $request, LokasiUnpam $lokasi)
    {
        $data = $request->validate([
            'nama_lokasi'  => ['required', 'string', 'max:150'],
            'latitude'     => ['required', 'numeric', 'between:-90,90'],
            'longitude'    => ['required', 'numeric', 'between:-180,180'],
            'radius_meter' => ['required', 'integer', 'min:10', 'max:5000'],
            'is_active'    => ['nullable'],
        ]);

        $setActive = $request->boolean('is_active');

        if ($setActive) {
            LokasiUnpam::query()->where('id', '!=', $lokasi->id)->update(['is_active' => 0]);
        }

        $lokasi->update([
            'nama_lokasi'  => $data['nama_lokasi'],
            'latitude'     => $data['latitude'],
            'longitude'    => $data['longitude'],
            'radius_meter' => $data['radius_meter'],
            'is_active'    => $setActive ? 1 : 0,
        ]);

        return back()->with('success', 'Lokasi berhasil diupdate.');
    }

    public function setActive(LokasiUnpam $lokasi)
    {
        LokasiUnpam::query()->update(['is_active' => 0]);
        $lokasi->update(['is_active' => 1]);

        return back()->with('success', 'Lokasi aktif berhasil diubah.');
    }

    public function destroy(LokasiUnpam $lokasi)
    {
        $lokasi->delete();
        return back()->with('success', 'Lokasi berhasil dihapus.');
    }
}
