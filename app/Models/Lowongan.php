<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    protected $primaryKey = 'id_lowongan';

    public function pendaftarans(){
        $this->hasMany(Pendaftaran::class);
    }

}
