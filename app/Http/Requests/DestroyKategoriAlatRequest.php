<?php

namespace App\Http\Requests;

use App\Models\KategoriAlat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestroyKategoriAlatRequest extends FormRequest
{
    /**
     * A kategori still used by alat or template form can only be deleted when they are moved to another kategori.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var KategoriAlat $kategoriAlat */
        $kategoriAlat = $this->route('kategoriAlat');

        return [
            'pindah_ke' => [
                Rule::requiredIf(fn (): bool => $kategoriAlat->masihDipakai()),
                'nullable',
                'integer',
                Rule::exists('kategori_alat', 'id'),
                Rule::notIn([$kategoriAlat->id]),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pindah_ke.required' => 'Pilih kategori tujuan untuk alat dan form yang masih memakai kategori ini.',
            'pindah_ke.exists' => 'Kategori tujuan tidak ditemukan.',
            'pindah_ke.not_in' => 'Kategori tujuan harus berbeda dari kategori yang dihapus.',
        ];
    }
}
