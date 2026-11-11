<?php

namespace App\Models;

use Database\Factories\TugasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'tugas', timestamps: false)]
#[Fillable(['id_template_form', 'nama'])]
class Tugas extends Model
{
    /** @use HasFactory<TugasFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<TemplateForm, $this>
     */
    public function templateForm(): BelongsTo
    {
        return $this->belongsTo(TemplateForm::class, 'id_template_form');
    }
}
