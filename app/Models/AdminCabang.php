<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $nama_ac
 * @property string $email_ac
 * @property string $password_ac
 * @property string $telp_ac
 * @property int $cabang_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang whereCabangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang whereEmailAc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang whereNamaAc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang wherePasswordAc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang whereTelpAc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminCabang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminCabang extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = ["cabang_id","nama_ac", "telp_ac", "email_ac","password_ac"];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, "cabang_id", "id_cabang");
    }

    public function lowongans()
    {
       return $this->hasMany(Lowongan::class, "admin_cabang_id", "id");
    }

}
