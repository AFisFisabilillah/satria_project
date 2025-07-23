<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id_lowongan
 * @property int $admin_cabang_id
 * @property string $nama_lowongan
 * @property string $deskripsi_lowongan
 * @property string $syarat_lowongan
 * @property string $negara_lowongan
 * @property string $posisi_lowongan
 * @property string $jam_kerja
 * @property int $gaji_lowongan
 * @property string $deadline_lowongan
 * @property string $kontrak_lowongan
 * @property string $lokasi_lowongan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereAdminCabangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereDeadlineLowongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereDeskripsiLowongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereGajiLowongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereIdLowongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereJamKerja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereKontrakLowongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereLokasiLowongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereNamaLowongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereNegaraLowongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan wherePosisiLowongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereSyaratLowongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lowongan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Lowongan extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'id_lowongan';
    protected $fillable = ["admin_cabang_id", "nama_lowongan", "deskripsi_lowongan", "syarat_lowongan","posisi_lowongan","gaji_lowongan", "deadline_lowongan", "negara_lowongan", "kontrak_lowongan", "lokasi_lowongan", "currency", "kuota_lowongan", "status_lowongan"];

    public function pendaftarans(){
        return $this->hasMany(Pendaftaran::class, "lowongan_id", "id_lowongan");
    }

    public function adminCabang()
    {
        $this->belongsTo(AdminCabang::class, 'admin_cabang_id', "id");
    }

}
