<?php

namespace App\Http\Requests;

use App\Models\Pengguna;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePenggunaRequest extends FormRequest
{
    /**
     * The username is the primary key and appears in URLs, so it only allows letters, numbers, `-` and `_`.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'alpha_dash:ascii', 'max:20', 'unique:pengguna,id'],
            'nama' => ['required', 'string', 'max:100'],
            'peran' => ['required', Rule::in(array_keys(Pengguna::DAFTAR_PERAN))],
            'kata_sandi' => ['required', 'string', 'min:8'],
            'aktif' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id.required' => 'Username wajib diisi.',
            'id.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda - dan _.',
            'id.max' => 'Username maksimal 20 karakter.',
            'id.unique' => 'Username ini sudah dipakai.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'peran.required' => 'Peran wajib dipilih.',
            'peran.in' => 'Peran tidak valid.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
            'kata_sandi.min' => 'Kata sandi minimal 8 karakter.',
            'aktif.boolean' => 'Status akun tidak valid.',
        ];
    }
}
