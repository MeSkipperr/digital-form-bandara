<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenggunaRequest;
use App\Http\Requests\UpdatePenggunaRequest;
use App\Models\Pengguna;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenggunaController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci = trim((string) $request->query('cari'));

        $daftarPengguna = Pengguna::query()
            ->when($kataKunci !== '', function ($query) use ($kataKunci): void {
                $query->where(function ($query) use ($kataKunci): void {
                    $query->where('nama', 'like', "%{$kataKunci}%")
                        ->orWhere('id', 'like', "%{$kataKunci}%");
                });
            })
            ->orderBy('nama')
            ->paginate(self::JUMLAH_PER_HALAMAN, pageName: 'halaman')
            ->withQueryString();

        return view('admin.pengguna.index', [
            'daftarPengguna' => $daftarPengguna,
            'daftarPeran' => Pengguna::DAFTAR_PERAN,
        ]);
    }

    public function store(StorePenggunaRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('kata_sandi');
        $data['kata_sandi_hash'] = Pengguna::hashKataSandi($request->validated('kata_sandi'));

        Pengguna::create($data + ['aktif' => true]);

        return back()->with('status', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Update the pengguna. The login session stores the username, so a pengguna renaming their own account is logged
     * in again under the new username instead of being logged out.
     */
    public function update(UpdatePenggunaRequest $request, Pengguna $pengguna): RedirectResponse
    {
        $data = $request->safe()->except('kata_sandi');
        $kataSandi = $request->validated('kata_sandi');
        $isPenggunaYangMasuk = Auth::id() === $pengguna->id;

        if (filled($kataSandi)) {
            $data['kata_sandi_hash'] = Pengguna::hashKataSandi($kataSandi);
        }

        $pengguna->update($data);

        if ($isPenggunaYangMasuk) {
            Auth::login($pengguna);
        }

        return back()->with('status', 'Pengguna berhasil diperbarui.');
    }
}
