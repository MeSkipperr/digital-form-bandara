<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePenggunaRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'peran' => ['required', Rule::in(['admin', 'petugas'])],
            'kata_sandi' => ['nullable', 'string', 'min:8'],
            'aktif' => ['boolean'],
        ];
    }
}
