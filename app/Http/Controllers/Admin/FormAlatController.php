<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormAlatRequest;
use App\Models\Alat;
use App\Models\FormAlat;
use App\Models\TemplateForm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * "Kelola Form" of one alat: the template forms assigned to it (form_alat), which the daily job turns into riwayat.
 */
class FormAlatController extends Controller
{
    /**
     * Assigned forms, plus the unassigned template forms of the alat's kategori for the "Tambah Form" modal.
     */
    public function index(Alat $alat): View
    {
        $daftarFormAlat = $alat->formAlat()
            ->with(['templateForm' => fn ($query) => $query->withCount('tugas')])
            ->withCount(['riwayatPengecekan as riwayat_tercatat_count' => fn ($query) => $query->tercatat()])
            ->get()
            ->sortBy('templateForm.nama')
            ->values();

        $daftarFormTersedia = TemplateForm::query()
            ->where('id_kategori_alat', $alat->id_kategori_alat)
            ->whereNotIn('id', $daftarFormAlat->pluck('id_template_form'))
            ->withCount('tugas')
            ->orderBy('nama')
            ->get();

        return view('admin.alat.form', [
            'alat' => $alat->load('kategoriAlat'),
            'daftarFormAlat' => $daftarFormAlat,
            'daftarFormTersedia' => $daftarFormTersedia,
        ]);
    }

    public function store(StoreFormAlatRequest $request, Alat $alat): RedirectResponse
    {
        $alat->formAlat()->createMany(
            collect($request->validated('id_template_form'))->map(fn (int|string $idTemplateForm): array => ['id_template_form' => $idTemplateForm])
        );

        return redirect()->route('admin.alat.form.index', $alat)->with('status', 'Form berhasil dipasang ke alat.');
    }

    /**
     * The tasks of one assigned form. Tasks belong to the template form, so they are managed on its Kelola Tugas page.
     */
    public function show(Alat $alat, FormAlat $formAlat): View
    {
        abort_unless($formAlat->id_alat === $alat->id, 404);

        $templateForm = $formAlat->templateForm;

        return view('admin.alat.task', [
            'alat' => $alat,
            'templateForm' => $templateForm,
            'daftarTugas' => $templateForm->tugas()->orderBy('id')->get(),
        ]);
    }

    /**
     * Unassign a form. Riwayat an officer already filled stays as history, so such a form cannot be removed.
     */
    public function destroy(Alat $alat, FormAlat $formAlat): RedirectResponse
    {
        abort_unless($formAlat->id_alat === $alat->id, 404);

        $namaForm = $formAlat->templateForm->nama;

        if ($formAlat->punyaRiwayatTercatat()) {
            return redirect()->route('admin.alat.form.index', $alat)
                ->withErrors(['form' => "{$namaForm} sudah memiliki riwayat pengecekan sehingga tidak dapat dilepas dari alat."]);
        }

        $formAlat->hapusBesertaRiwayatTertunda();

        return redirect()->route('admin.alat.form.index', $alat)->with('status', "{$namaForm} berhasil dilepas dari alat.");
    }
}
