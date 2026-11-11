<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlatRequest;
use App\Http\Requests\UpdateAlatRequest;
use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci = trim((string) $request->query('cari'));
        $kategoriAktif = $request->query('kategori');
        $statusAktif = $request->query('status');

        $daftarAlat = Alat::query()
            ->with('kategoriAlat')
            ->when($kataKunci !== '', function ($query) use ($kataKunci): void {
                $query->where(function ($query) use ($kataKunci): void {
                    $query->where('id', 'like', "%{$kataKunci}%")
                        ->orWhere('model', 'like', "%{$kataKunci}%")
                        ->orWhere('lokasi', 'like', "%{$kataKunci}%");
                });
            })
            ->when(filled($kategoriAktif), fn ($query) => $query->where('id_kategori_alat', $kategoriAktif))
            ->when(filled($statusAktif), fn ($query) => $query->where('status', $statusAktif))
            ->orderBy('id')
            ->get();

        return view('admin.alat.index', [
            'daftarAlat' => $daftarAlat,
            'daftarKategori' => KategoriAlat::query()->orderBy('id')->pluck('nama', 'id')->all(),
            'daftarStatus' => Alat::DAFTAR_STATUS,
            'kategoriAktif' => $kategoriAktif,
            'statusAktif' => $statusAktif,
        ]);
    }

    public function show(Alat $alat): View
    {
        $contoh = require resource_path('views/admin/alat/partials/contoh-data.php');

        return view('admin.alat.show', [
            'alat' => $alat->load('kategoriAlat'),
            'daftarKategori' => KategoriAlat::query()->orderBy('id')->pluck('nama', 'id')->all(),
            'daftarStatus' => Alat::DAFTAR_STATUS,
            'daftarForm' => array_slice($contoh['form'], 0, 3, true),
        ]);
    }

    public function store(StoreAlatRequest $request): RedirectResponse
    {
        $alat = Alat::create($request->validated() + ['status' => 'bagus']);

        return redirect()->route('admin.alat.show', $alat)->with('status', 'Alat berhasil ditambahkan.');
    }

    public function update(UpdateAlatRequest $request, Alat $alat): RedirectResponse
    {
        $alat->update($request->validated());

        return back()->with('status', 'Alat berhasil diperbarui.');
    }

    public function destroy(Alat $alat): RedirectResponse
    {
        $alat->delete();

        return redirect()->route('admin.alat.index')->with('status', 'Alat berhasil dihapus.');
    }

    public function formIndex(Alat $alat): View
    {
        $contoh = require resource_path('views/admin/alat/partials/contoh-data.php');

        return view('admin.alat.form', ['alat' => $alat, 'daftarForm' => $contoh['form']]);
    }

    public function task(Alat $alat, int $form): View
    {
        $contoh = require resource_path('views/admin/alat/partials/contoh-data.php');

        return view('admin.alat.task', [
            'alat' => $alat,
            'form' => $contoh['form'][$form] ?? abort(404),
            'daftarTask' => $contoh['task'],
        ]);
    }
}
