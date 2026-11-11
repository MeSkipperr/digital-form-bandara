<?php

namespace App\Models;

use Database\Factories\TemplateFormFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'template_form', timestamps: false)]
#[Fillable(['id_kategori_alat', 'path_gambar', 'nama'])]
class TemplateForm extends Model
{
    /** @use HasFactory<TemplateFormFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<KategoriAlat, $this>
     */
    public function kategoriAlat(): BelongsTo
    {
        return $this->belongsTo(KategoriAlat::class, 'id_kategori_alat');
    }

    /**
     * @return HasMany<Tugas, $this>
     */
    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'id_template_form');
    }

    /**
     * @return HasMany<FormAlat, $this>
     */
    public function formAlat(): HasMany
    {
        return $this->hasMany(FormAlat::class, 'id_template_form');
    }
}
