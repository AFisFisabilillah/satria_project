<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id_cabang
 * @property string $nama_cabang
 * @property string $alamat_cabang
 * @property string $kota_cabang
 * @property string $kepala_cabang
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang whereAlamatCabang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang whereIdCabang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang whereKepalaCabang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang whereKotaCabang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang whereNamaCabang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cabang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cabang extends Model
{
    use SoftDeletes, HasFactory;
    protected $primaryKey = 'id_cabang';
    protected $fillable = ['id_cabang','nama_cabang',"alamat_cabang","kota_cabang","kepala_cabang"];

    public function adminCabangs(){
        return $this->hasMany(AdminCabang::class, "cabang_id", "id_cabang");
    }
}
