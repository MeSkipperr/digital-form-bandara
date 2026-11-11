<?php

namespace App\Models;

use Database\Factories\RiwayatPengecekanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'riwayat_pengecekan', timestamps: false)]
#[Fillable(['id_form_alat', 'id_pengguna', 'catatan', 'status', 'dicek_pada'])]
class RiwayatPengecekan extends Model
{
    /** @use HasFactory<RiwayatPengecekanFactory> */
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'tertunda',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dicek_pada' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<FormAlat, $this>
     */
    public function formAlat(): BelongsTo
    {
        return $this->belongsTo(FormAlat::class, 'id_form_alat');
    }

    /**
     * @return BelongsTo<Pengguna, $this>
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}
