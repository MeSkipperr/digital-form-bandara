<?php

namespace App\Models;

use Database\Factories\AlatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Table(name: 'alat', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'id_kategori_alat', 'status', 'model', 'deskripsi', 'lokasi'])]
class Alat extends Model
{
    /** @use HasFactory<AlatFactory> */
    use HasFactory;

    /**
     * Label and badge color for each value of the `status` enum.
     *
     * @var array<string, array{label: string, warna: string}>
     */
    public const DAFTAR_STATUS = [
        'bagus' => ['label' => 'Baik', 'warna' => '#16A34A'],
        'perlu_perbaikan' => ['label' => 'Perlu Perbaikan', 'warna' => '#D97706'],
    ];

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
     * Assigned forms. A row whose template form was deleted only remains to keep its riwayat, so it is left out.
     *
     * @return HasMany<FormAlat, $this>
     */
    public function formAlat(): HasMany
    {
        return $this->hasMany(FormAlat::class, 'id_alat')->whereNotNull('id_template_form');
    }

    /**
     * @return HasManyThrough<RiwayatPengecekan, FormAlat, $this>
     */
    public function riwayatPengecekan(): HasManyThrough
    {
        return $this->hasManyThrough(RiwayatPengecekan::class, FormAlat::class, 'id_alat', 'id_form_alat');
    }
}
