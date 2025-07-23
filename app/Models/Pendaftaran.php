<?php

namespace App\Models;

use App\StatusPendaftaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use HasFactory, SoftDeletes;

    protected $fillable = ["pelamar_id", "lowongan_id", "waktu_pendaftaran", "status_pendaftaran"];
    protected  $primaryKey = "id_pendaftaran";

    protected $casts = [
        "status_pendaftaran" => StatusPendaftaran::class,
    ];


    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, "lowongan_id", "id_lowongan");
    }

    public function pelamar(){
        return $this->belongsTo(Pelamar::class, "pelamar_id", "id_pelamar");
    }

    public function statusHistories()
    {
        return $this->hasMany(StatusHistory::class, "pendaftaran_id", "id_pendaftaran");
    }
}
