<?php

namespace App\Http\Requests;

use App\Models\Pengguna;
use Illuminate\Validation\Rule;

class UpdatePenggunaRequest extends StorePenggunaRequest
{
    /**
     * The username cannot change; an empty `kata_sandi` keeps the current password.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'peran' => ['required', Rule::in(array_keys(Pengguna::DAFTAR_PERAN))],
            'kata_sandi' => ['nullable', 'string', 'min:8'],
            'aktif' => ['boolean'],
        ];
    }
}
