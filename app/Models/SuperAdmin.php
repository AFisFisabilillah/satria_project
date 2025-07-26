<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name_super_admin
 * @property string $email_super_admin
 * @property string $password_super_admin
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereEmailSuperAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereNameSuperAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin wherePasswordSuperAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SuperAdmin extends Model
{
    use HasFactory, HasApiTokens,SoftDeletes;

    protected $fillable = ["name_super_admin", "email_super_admin", "password_super_admin"];
}
