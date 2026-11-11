<?php

namespace App\Models;

use Database\Factories\KategoriAlatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'kategori_alat', timestamps: false)]
#[Fillable(['nama'])]
class KategoriAlat extends Model
{
    /** @use HasFactory<KategoriAlatFactory> */
    use HasFactory;

    /**
     * @return HasMany<Alat, $this>
     */
    public function alat(): HasMany
    {
        return $this->hasMany(Alat::class, 'id_kategori_alat');
    }

    /**
     * @return HasMany<TemplateForm, $this>
     */
    public function templateForm(): HasMany
    {
        return $this->hasMany(TemplateForm::class, 'id_kategori_alat');
    }
}
