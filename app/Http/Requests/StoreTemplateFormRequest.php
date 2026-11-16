<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTemplateFormRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'id_kategori_alat' => ['required', 'integer', 'exists:kategori_alat,id'],
            'gambar' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama form wajib diisi.',
            'nama.max' => 'Nama form maksimal 100 karakter.',
            'id_kategori_alat.required' => 'Kategori wajib dipilih.',
            'id_kategori_alat.exists' => 'Kategori tidak ditemukan.',
            'gambar.uploaded' => 'Gambar gagal diunggah. Ukuran file mungkin melebihi batas upload server.',
            'gambar.image' => 'Gambar harus berupa file PNG atau JPG.',
            'gambar.mimes' => 'Gambar harus berupa file PNG atau JPG.',
            'gambar.max' => 'Ukuran gambar maksimal 5 MB.',
        ];
    }
}
