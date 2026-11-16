<?php

namespace App\Http\Requests;

use App\Models\Alat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormAlatRequest extends FormRequest
{
    /**
     * Template forms checked in the "Tambah Form" modal: they must belong to the alat's kategori and not be assigned yet.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Alat $alat */
        $alat = $this->route('alat');

        return [
            'id_template_form' => ['required', 'array', 'min:1'],
            'id_template_form.*' => [
                'integer',
                'distinct',
                Rule::exists('template_form', 'id')->where('id_kategori_alat', $alat->id_kategori_alat),
                Rule::unique('form_alat', 'id_template_form')->where('id_alat', $alat->id),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_template_form.required' => 'Pilih minimal satu form.',
            'id_template_form.min' => 'Pilih minimal satu form.',
            'id_template_form.*.exists' => 'Form tidak ditemukan untuk kategori alat ini.',
            'id_template_form.*.unique' => 'Form ini sudah dipasang di alat.',
        ];
    }
}
