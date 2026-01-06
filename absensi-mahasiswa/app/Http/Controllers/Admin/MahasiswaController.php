<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    /**
     * Tampilkan data mahasiswa
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $mahasiswa = Mahasiswa::query()
            ->when($q, function ($query) use ($q) {
                $query->where('nim', 'like', "%{$q}%")
                      ->orWhere('nama', 'like', "%{$q}%")
                      ->orWhere('prodi', 'like', "%{$q}%")
                      ->orWhere('kelas', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.mahasiswa', compact('mahasiswa', 'q'));
    }

    /**
     * Simpan mahasiswa baru + buat akun user otomatis
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nim'   => ['required', 'string', 'max:20', 'unique:mahasiswa,nim'],
            'nama'  => ['nullable', 'string', 'max:255'],
            'prodi' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:50'],
        ]);

        // password default: #unpam + 6 digit terakhir NIM
        $password = '#unpam' . substr($data['nim'], -6);

        // buat akun user mahasiswa
        $user = User::create([
            'name'     => $data['nama'] ?? 'Mahasiswa',
            'email'    => $data['nim'] . '@mahasiswa.unpam.ac.id',
            'password' => Hash::make($password),
            'role'     => 'mahasiswa',
        ]);

        // simpan data mahasiswa
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim'     => $data['nim'],
            'nama'    => $data['nama'],
            'prodi'   => $data['prodi'],
            'kelas'   => $data['kelas'],
        ]);

        return back()->with(
            'success',
            "Mahasiswa berhasil ditambahkan. Password default: {$password}"
        );
    }

    /**
     * Update data mahasiswa
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $data = $request->validate([
            'nim' => [
                'required', 'string', 'max:20',
                Rule::unique('mahasiswa', 'nim')->ignore($mahasiswa->id),
            ],
            'nama'  => ['nullable', 'string', 'max:255'],
            'prodi' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:50'],
        ]);

        // jika NIM berubah, update email user
        if ($mahasiswa->nim !== $data['nim']) {
            $mahasiswa->user->update([
                'email' => $data['nim'] . '@mahasiswa.unpam.ac.id',
            ]);
        }

        $mahasiswa->update($data);

        return back()->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    /**
     * Hapus mahasiswa + akun user
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        // hapus akun user
        $mahasiswa->user()->delete();

        // hapus data mahasiswa
        $mahasiswa->delete();

        return back()->with('success', 'Mahasiswa dan akun login berhasil dihapus.');
    }
}