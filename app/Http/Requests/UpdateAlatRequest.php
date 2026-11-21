<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateAlatRequest extends StoreAlatRequest
{
    /**
     * Same fields as tambah. The ID may change, but must not be used by another alat.
     *
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'id' => ['required', 'string', 'alpha_dash:ascii', 'max:20', Rule::unique('alat', 'id')->ignore($this->route('alat')->id, 'id')],
        ];
    }
}
