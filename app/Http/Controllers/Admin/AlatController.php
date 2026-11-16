<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlatRequest;
use App\Http\Requests\UpdateAlatRequest;
use App\Models\Alat;
use App\Models\FormAlat;
use App\Models\KategoriAlat;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlatController extends Controller
{
    /**
     * Assigned forms shown on the detail page before "Lihat Semua Form".
     */
    protected const JUMLAH_PRATINJAU_FORM = 4;

    public function index(Request $request): View
    {
        $kataKunci = trim((string) $request->query('cari'));
        $kategoriAktif = $request->query('kategori');
        $statusAktif = $request->query('status');

        $daftarAlat = Alat::query()
            ->with('kategoriAlat')
            ->withCount(['riwayatPengecekan as riwayat_tercatat_count' => fn ($query) => $query->tercatat()])
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
            ->paginate(self::JUMLAH_PER_HALAMAN, pageName: 'halaman')
            ->withQueryString();

        return view('admin.alat.index', [
            'daftarAlat' => $daftarAlat,
            'daftarKategori' => $this->daftarKategori(),
            'daftarStatus' => Alat::DAFTAR_STATUS,
            'kategoriAktif' => $kategoriAktif,
            'statusAktif' => $statusAktif,
        ]);
    }

    /**
     * Detail page with a preview of the first assigned forms; "Lihat Semua Form" opens the full Kelola Form page.
     */
    public function show(Alat $alat): View
    {
        return view('admin.alat.show', [
            'alat' => $alat->load('kategoriAlat'),
            'daftarKategori' => $this->daftarKategori(),
            'daftarStatus' => Alat::DAFTAR_STATUS,
            'daftarFormAlat' => $alat->formAlat()
                ->with('templateForm')
                ->get()
                ->sortBy('templateForm.nama')
                ->take(self::JUMLAH_PRATINJAU_FORM)
                ->values(),
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

    /**
     * Delete the alat with its assigned forms and their pending riwayat. Riwayat an officer already filled stays as
     * history, so such an alat cannot be deleted.
     */
    public function destroy(Alat $alat): RedirectResponse
    {
        if ($alat->riwayatPengecekan()->tercatat()->exists()) {
            return redirect()->route('admin.alat.index')
                ->withErrors(['alat' => "Alat {$alat->id} sudah memiliki riwayat pengecekan sehingga tidak dapat dihapus."]);
        }

        DB::transaction(function () use ($alat): void {
            $alat->formAlat->each(fn (FormAlat $formAlat) => $formAlat->hapusBesertaRiwayatTertunda());
            $alat->delete();
        });

        return redirect()->route('admin.alat.index')->with('status', 'Alat berhasil dihapus.');
    }

    /**
     * Kategori options for the alat form dropdown, with usage counts for the delete confirmation.
     *
     * @return Collection<int, KategoriAlat>
     */
    protected function daftarKategori(): Collection
    {
        return KategoriAlat::query()->withCount(['alat', 'templateForm'])->orderBy('id')->get();
    }
}
