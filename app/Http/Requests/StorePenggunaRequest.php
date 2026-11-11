<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePenggunaRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'max:20', 'unique:pengguna,id'],
            'nama' => ['required', 'string', 'max:100'],
            'peran' => ['required', Rule::in(['admin', 'petugas'])],
            'kata_sandi' => ['required', 'string', 'min:8'],
            'aktif' => ['boolean'],
        ];
    }
}
