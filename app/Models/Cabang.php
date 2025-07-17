<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    //
}
