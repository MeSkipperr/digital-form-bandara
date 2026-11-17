<?php

namespace App\Models;

use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

#[Table(name: 'pengguna', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'nama', 'peran', 'kata_sandi_hash', 'aktif'])]
#[Hidden(['kata_sandi_hash'])]
class Pengguna extends Authenticatable  
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
     * Hash a plain password with the HASH_PASSWORD_KEY secret (config `hashing.password_key`) mixed in.
     */
    public static function hashKataSandi(#[\SensitiveParameter] string $kataSandi): string
    {
        return Hash::make(static::campurKunciKataSandi($kataSandi));
    }

    /**
     * Whether the plain password matches the stored hash, using the same HASH_PASSWORD_KEY secret.
     */
    public function cocokKataSandi(#[\SensitiveParameter] string $kataSandi): bool
    {
        return Hash::check(static::campurKunciKataSandi($kataSandi), $this->kata_sandi_hash);
    }

    /**
     * HMAC-SHA256 of the password keyed with HASH_PASSWORD_KEY, or the password itself when no key is set.
     * The 64-character hex digest stays under the 72-byte bcrypt limit.
     */
    protected static function campurKunciKataSandi(#[\SensitiveParameter] string $kataSandi): string
    {
        $kunci = (string) config('hashing.password_key');

        return $kunci === '' ? $kataSandi : hash_hmac('sha256', $kataSandi, $kunci);
    }

    /**
     * A plain password assigned to `kata_sandi_hash` is hashed with the key; an existing hash is stored as is.
     *
     * @return Attribute<string, string>
     */
    protected function kataSandiHash(): Attribute
    {
        return Attribute::make(
            set: fn (#[\SensitiveParameter] string $kataSandi): string => Hash::isHashed($kataSandi) ? $kataSandi : static::hashKataSandi($kataSandi),
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
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
