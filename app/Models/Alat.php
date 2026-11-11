<?php

namespace App\Models;

use Database\Factories\AlatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'alat', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'id_kategori_alat', 'status', 'model', 'deskripsi', 'lokasi'])]
class Alat extends Model
{
    /** @use HasFactory<AlatFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dibuat_pada' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<KategoriAlat, $this>
     */
    public function kategoriAlat(): BelongsTo
    {
        return $this->belongsTo(KategoriAlat::class, 'id_kategori_alat');
    }

    /**
     * @return HasMany<FormAlat, $this>
     */
    public function formAlat(): HasMany
    {
        return $this->hasMany(FormAlat::class, 'id_alat');
    }
}
