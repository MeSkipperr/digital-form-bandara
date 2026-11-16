<?php

namespace App\Http\Requests;

use Illuminate\Support\Arr;

class UpdateAlatRequest extends StoreAlatRequest
{
    /**
     * Same fields as tambah except the ID, which cannot change.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return Arr::except(parent::rules(), 'id');
    }
}
