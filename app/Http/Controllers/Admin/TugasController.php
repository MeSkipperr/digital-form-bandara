<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTugasRequest;
use App\Http\Requests\UpdateTugasRequest;
use App\Models\TemplateForm;
use App\Models\Tugas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * "Kelola Tugas" page from the 06 designs: the tasks of one template form with tambah, ubah and hapus modals.
 */
class TugasController extends Controller
{
    public function index(TemplateForm $templateForm): View
    {
        return view('admin.form.tugas', [
            'templateForm' => $templateForm,
            'daftarTugas' => $templateForm->tugas()->orderBy('id')->get(),
        ]);
    }

    public function store(StoreTugasRequest $request, TemplateForm $templateForm): RedirectResponse
    {
        $templateForm->tugas()->create($request->validated());

        return redirect()->route('admin.form.tugas.index', $templateForm)->with('status', 'Task berhasil ditambahkan.');
    }

    public function update(UpdateTugasRequest $request, TemplateForm $templateForm, Tugas $tugas): RedirectResponse
    {
        $tugas->update($request->validated());

        return redirect()->route('admin.form.tugas.index', $templateForm)->with('status', 'Task berhasil diperbarui.');
    }

    public function destroy(TemplateForm $templateForm, Tugas $tugas): RedirectResponse
    {
        $tugas->delete();

        return redirect()->route('admin.form.tugas.index', $templateForm)->with('status', 'Task berhasil dihapus.');
    }
}
