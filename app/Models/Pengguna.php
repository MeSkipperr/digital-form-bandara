<?php

namespace App\Models;

use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'pengguna', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'nama', 'peran', 'kata_sandi_hash', 'aktif'])]
#[Hidden(['kata_sandi_hash'])]
class Pengguna extends Model
{
    /** @use HasFactory<PenggunaFactory> */
    use HasFactory;

    /**
     * Label for each value of the `peran` enum.
     *
     * @var array<string, string>
     */
    public const DAFTAR_PERAN = [
        'petugas' => 'Petugas',
        'admin' => 'Admin',
    ];

    /**
     * Initials shown in the avatar, taken from the first two words of the name.
     */
    public function inisial(): string
    {
        return collect(explode(' ', trim($this->nama)))
            ->filter()
            ->take(2)
            ->map(fn (string $kata): string => mb_strtoupper(mb_substr($kata, 0, 1)))
            ->implode('');
    }

    /**
     * Avatar background color, stable per user.
     */
    public function warnaAvatar(): string
    {
        $warna = ['#12345A', '#1D4E89', '#19A7A0', '#475569', '#0F766E', '#334155'];

        return $warna[crc32($this->id) % count($warna)];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kata_sandi_hash' => 'hashed',
            'aktif' => 'boolean',
            'dibuat_pada' => 'datetime',
        ];
    }

    /**
     * @return HasMany<RiwayatPengecekan, $this>
     */
    public function riwayatPengecekan(): HasMany
    {
        return $this->hasMany(RiwayatPengecekan::class, 'id_pengguna');
    }

    /**
     * @return HasMany<SesiPengguna, $this>
     */
    public function sesiPengguna(): HasMany
    {
        return $this->hasMany(SesiPengguna::class, 'id_pengguna');
    }
}
