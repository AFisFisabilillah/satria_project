<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_pendaftaran
 * @property int $pelamar_id
 * @property int $lowongan_id
 * @property string $waktu_pendaftaran
 * @property string $status_pendaftaran
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftaran whereIdPendaftaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftaran whereLowonganId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftaran wherePelamarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftaran whereStatusPendaftaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftaran whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftaran whereWaktuPendaftaran($value)
 * @mixin \Eloquent
 */
class Pendaftaran extends Model
{
    use HasFactory;

    protected  $primaryKey = "id_pendaftaran";

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class);
    }

    public function pelamar(){
        return $this->belongsTo(Pelamar::class);
    }
}
