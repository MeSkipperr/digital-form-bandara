<?php

namespace Database\Factories;

use App\Models\TemplateForm;
use App\Models\Tugas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tugas>
 */
class TugasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_template_form' => TemplateForm::factory(),
            'nama' => fake()->randomElement([
                'Periksa kejernihan citra monitor',
                'Uji deteksi dengan benda uji',
                'Periksa fungsi conveyor belt',
                'Periksa kondisi baterai',
                'Periksa kalibrasi sensor',
                'Uji sensitivitas alarm',
                'Periksa kebersihan perangkat',
                'Periksa kabel dan konektor',
            ]),
        ];
    }
}
