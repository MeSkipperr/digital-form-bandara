<?php

namespace App\Http\Requests;

use App\Models\Pengguna;
use Illuminate\Validation\Rule;

class UpdatePenggunaRequest extends StorePenggunaRequest
{
    /**
     * The username may change, but must not be used by another pengguna; an empty `kata_sandi` keeps the current
     * password.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'alpha_dash:ascii', 'max:20', Rule::unique('pengguna', 'id')->ignore($this->route('pengguna')->id, 'id')],
            'nama' => ['required', 'string', 'max:100'],
            'peran' => ['required', Rule::in(array_keys(Pengguna::DAFTAR_PERAN))],
            'kata_sandi' => ['nullable', 'string', 'min:8'],
            'aktif' => ['boolean'],
        ];
    }
}
