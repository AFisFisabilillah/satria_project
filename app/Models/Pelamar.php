<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin EloquentBuilder
 * @mixin QueryBuilder
 * @property int $id_pelamar
 * @property string $nama_pelamar
 * @property string $email_pelamar
 * @property string $telp_pelamar
 * @property string|null $ttl_pelamar
 * @property string $domisili_pelamar
 * @property string|null $status_nikah_pelamar
 * @property string|null $profile_pelamar
 * @property string|null $cv_pelamar
 * @property string $password_pelamar
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static EloquentBuilder<static>|Pelamar newModelQuery()
 * @method static EloquentBuilder<static>|Pelamar newQuery()
 * @method static EloquentBuilder<static>|Pelamar query()
 * @method static EloquentBuilder<static>|Pelamar whereCreatedAt($value)
 * @method static EloquentBuilder<static>|Pelamar whereCvPelamar($value)
 * @method static EloquentBuilder<static>|Pelamar whereDeletedAt($value)
 * @method static EloquentBuilder<static>|Pelamar whereDomisiliPelamar($value)
 * @method static EloquentBuilder<static>|Pelamar whereEmailPelamar($value)
 * @method static EloquentBuilder<static>|Pelamar whereIdPelamar($value)
 * @method static EloquentBuilder<static>|Pelamar whereNamaPelamar($value)
 * @method static EloquentBuilder<static>|Pelamar wherePasswordPelamar($value)
 * @method static EloquentBuilder<static>|Pelamar whereProfilePelamar($value)
 * @method static EloquentBuilder<static>|Pelamar whereProvinsiPelamar($value)
 * @method static EloquentBuilder<static>|Pelamar whereStatusNikahPelamar($value)
 * @method static EloquentBuilder<static>|Pelamar whereTelpPelamar($value)
 * @method static EloquentBuilder<static>|Pelamar whereTtlPelamar($value)
 * @method static EloquentBuilder<static>|Pelamar whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Pelamar extends Model
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $primaryKey = "id_pelamar";
    protected $fillable = ['nama_pelamar', 'email_pelamar', 'telp_pelamar','provinsi_pelamar','ttl_pelamar','domisili_pelamar','status_nikah_pelamar','profile_pelamar','cv_pelamar','password_pelamar'];

    protected $guarded = ['id_pelamar'];

    public function pendaftarans()
    {
       return $this->hasMany(Pendaftaran::class, "pelamar_id",'id_pelamar' );
    }

     public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }

    // Pesan yang diterima oleh pelamar
    public function receivedMessages()
    {
        return $this->morphMany(Message::class, 'receiver');
    }
}
