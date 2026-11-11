<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenggunaRequest;
use App\Http\Requests\UpdatePenggunaRequest;
use App\Models\Pengguna;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
            ->paginate(6, pageName: 'halaman')
            ->withQueryString();

        return view('admin.pengguna.index', [
            'daftarPengguna' => $daftarPengguna,
            'daftarPeran' => Pengguna::DAFTAR_PERAN,
        ]);
    }

    public function store(StorePenggunaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['kata_sandi_hash'] = $data['kata_sandi'];
        unset($data['kata_sandi']);

        Pengguna::create($data + ['aktif' => true]);

        return back()->with('status', 'Pengguna berhasil ditambahkan.');
    }

    public function update(UpdatePenggunaRequest $request, Pengguna $pengguna): RedirectResponse
    {
        $data = $request->validated();
        $kataSandi = $data['kata_sandi'] ?? null;
        unset($data['kata_sandi']);

        if (filled($kataSandi)) {
            $data['kata_sandi_hash'] = $kataSandi;
        }

        $pengguna->update($data);

        return back()->with('status', 'Pengguna berhasil diperbarui.');
    }
}
