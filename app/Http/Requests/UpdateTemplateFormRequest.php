<?php

namespace App\Http\Requests;

class UpdateTemplateFormRequest extends StoreTemplateFormRequest
{
    /**
     * Same fields as tambah, plus `hapus_gambar` from the "Hapus gambar" button of the Ubah Form modal.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return parent::rules() + [
            'hapus_gambar' => ['nullable', 'boolean'],
        ];
    }
}
