<?php

namespace App\Models;

use Database\Factories\FormAlatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'form_alat', timestamps: false)]
#[Fillable(['id_alat', 'id_template_form'])]
class FormAlat extends Model
{
    /** @use HasFactory<FormAlatFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Alat, $this>
     */
    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'id_alat');
    }

    /**
     * @return BelongsTo<TemplateForm, $this>
     */
    public function templateForm(): BelongsTo
    {
        return $this->belongsTo(TemplateForm::class, 'id_template_form');
    }

    /**
     * @return HasMany<RiwayatPengecekan, $this>
     */
    public function riwayatPengecekan(): HasMany
    {
        return $this->hasMany(RiwayatPengecekan::class, 'id_form_alat');
    }
}
