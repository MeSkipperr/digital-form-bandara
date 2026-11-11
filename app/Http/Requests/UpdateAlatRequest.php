<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlatRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'id_kategori_alat' => ['required', 'integer', 'exists:kategori_alat,id'],
            'model' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
