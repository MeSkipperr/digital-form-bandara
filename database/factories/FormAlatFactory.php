<?php

namespace Database\Factories;

use App\Models\Alat;
use App\Models\FormAlat;
use App\Models\TemplateForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormAlat>
 */
class FormAlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_alat' => Alat::factory(),
            'id_template_form' => TemplateForm::factory(),
        ];
    }
}
