<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyKategoriAlatRequest;
use App\Http\Requests\StoreKategoriAlatRequest;
use App\Http\Requests\UpdateKategoriAlatRequest;
use App\Models\KategoriAlat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/**
 * Kategori management used inline by the Kategori dropdown of the alat form and the Kelola Kategori modal.
 * Answers JSON for the fetch calls from app.js and redirects back for plain form posts.
 */
class KategoriAlatController extends Controller
{
    public function store(StoreKategoriAlatRequest $request): JsonResponse|RedirectResponse
    {
        $kategoriAlat = KategoriAlat::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json($kategoriAlat->only(['id', 'nama']), 201);
        }

        return back()->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function update(UpdateKategoriAlatRequest $request, KategoriAlat $kategoriAlat): JsonResponse|RedirectResponse
    {
        $kategoriAlat->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json($kategoriAlat->only(['id', 'nama']));
        }

        return back()->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(DestroyKategoriAlatRequest $request, KategoriAlat $kategoriAlat): JsonResponse|RedirectResponse
    {
        $idTujuan = $request->validated('pindah_ke');

        DB::transaction(function () use ($kategoriAlat, $idTujuan): void {
            if ($idTujuan !== null) {
                $kategoriAlat->alat()->update(['id_kategori_alat' => $idTujuan]);
                $kategoriAlat->templateForm()->update(['id_kategori_alat' => $idTujuan]);
            }

            $kategoriAlat->delete();
        });

        if ($request->expectsJson()) {
            return response()->json(['id' => $kategoriAlat->id, 'pindah_ke' => $idTujuan]);
        }

        return back()->with('status', 'Kategori berhasil dihapus.');
    }
}
