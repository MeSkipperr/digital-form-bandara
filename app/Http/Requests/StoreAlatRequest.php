<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlatRequest extends FormRequest
{
    /**
     * The ID is the primary key and appears in URLs and the QR code, so it only allows letters, numbers, `-` and `_`.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'alpha_dash:ascii', 'max:20', 'unique:alat,id'],
            'id_kategori_alat' => ['required', 'integer', 'exists:kategori_alat,id'],
            'model' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id.required' => 'ID alat wajib diisi.',
            'id.alpha_dash' => 'ID alat hanya boleh berisi huruf, angka, tanda - dan _.',
            'id.max' => 'ID alat maksimal 20 karakter.',
            'id.unique' => 'ID alat ini sudah dipakai.',
            'id_kategori_alat.required' => 'Kategori wajib dipilih.',
            'id_kategori_alat.integer' => 'Kategori tidak valid.',
            'id_kategori_alat.exists' => 'Kategori tidak ditemukan.',
            'model.max' => 'Model maksimal 100 karakter.',
            'lokasi.max' => 'Lokasi maksimal 100 karakter.',
        ];
    }
}
