<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlatRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'max:20', 'unique:alat,id'],
            'id_kategori_alat' => ['required', 'integer', 'exists:kategori_alat,id'],
            'model' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
