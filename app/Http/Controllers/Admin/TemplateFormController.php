<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTemplateFormRequest;
use App\Http\Requests\UpdateTemplateFormRequest;
use App\Models\KategoriAlat;
use App\Models\TemplateForm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * "Kelola Form" page from the 05 designs: template form cards with tambah, ubah and hapus modals.
 */
class TemplateFormController extends Controller
{
    /**
     * Folder on the public disk that holds uploaded form images.
     */
    protected const FOLDER_GAMBAR = 'template-form';

    /**
     * List every template form with its task and alat counts, filterable by kategori.
     */
    public function index(Request $request): View
    {
        $kategoriAktif = $request->query('kategori');

        $daftarForm = TemplateForm::query()
            ->with('kategoriAlat')
            ->withCount(['tugas', 'formAlat'])
            ->when(filled($kategoriAktif), fn ($query) => $query->where('id_kategori_alat', $kategoriAktif))
            ->orderBy('nama')
            ->orderBy('id')
            ->paginate(self::JUMLAH_PER_HALAMAN, pageName: 'halaman')
            ->withQueryString();

        return view('admin.form.index', [
            'daftarForm' => $daftarForm,
            'daftarKategori' => KategoriAlat::query()->withCount(['alat', 'templateForm'])->orderBy('id')->get(),
            'kategoriAktif' => $kategoriAktif,
        ]);
    }

    public function store(StoreTemplateFormRequest $request): RedirectResponse
    {
        $templateForm = TemplateForm::create([
            ...$request->safe()->only(['nama', 'id_kategori_alat']),
            'path_gambar' => $request->file('gambar')?->store(self::FOLDER_GAMBAR, 'public'),
        ]);

        return redirect()->route('admin.form.tugas.index', $templateForm)->with('status', 'Form berhasil ditambahkan.');
    }

    /**
     * Update the name and kategori, and replace or remove the image when asked to.
     */
    public function update(UpdateTemplateFormRequest $request, TemplateForm $templateForm): RedirectResponse
    {
        $data = $request->safe()->only(['nama', 'id_kategori_alat']);
        $pathLama = $templateForm->path_gambar;

        if ($request->hasFile('gambar')) {
            $data['path_gambar'] = $request->file('gambar')->store(self::FOLDER_GAMBAR, 'public');
        } elseif ($request->boolean('hapus_gambar')) {
            $data['path_gambar'] = null;
        }

        $templateForm->update($data);

        if ($pathLama !== $templateForm->path_gambar) {
            $this->hapusGambarTakTerpakai($pathLama);
        }

        return redirect()->route('admin.form.index')->with('status', 'Form berhasil diperbarui.');
    }

    /**
     * Delete the form together with its tasks. A form still assigned to alat keeps its riwayat, so it cannot be deleted.
     */
    public function destroy(TemplateForm $templateForm): RedirectResponse
    {
        if ($templateForm->formAlat()->exists()) {
            return redirect()->route('admin.form.index')
                ->withErrors(['form' => "{$templateForm->nama} masih dipakai alat sehingga tidak dapat dihapus."]);
        }

        DB::transaction(function () use ($templateForm): void {
            $templateForm->tugas()->delete();
            $templateForm->delete();
        });

        $this->hapusGambarTakTerpakai($templateForm->path_gambar);

        return redirect()->route('admin.form.index')->with('status', 'Form berhasil dihapus.');
    }

    /**
     * Remove an image from the public disk unless another form still points at it.
     */
    protected function hapusGambarTakTerpakai(?string $path): void
    {
        if (blank($path) || TemplateForm::query()->where('path_gambar', $path)->exists()) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
