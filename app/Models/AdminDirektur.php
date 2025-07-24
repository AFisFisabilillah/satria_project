<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $nama_direktur
 * @property string $email_direktur
 * @property string $telp_direktur
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDirektur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDirektur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDirektur query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDirektur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDirektur whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDirektur whereEmailDirektur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDirektur whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDirektur whereNamaDirektur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDirektur whereTelpDirektur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDirektur whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminDirektur extends Model
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;
    protected $fillable = ["nama_direktur", "email_direktur", "telp_direktur", "jabatan_direktur", "password_direktur"];
}
