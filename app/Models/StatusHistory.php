<?php

namespace App\Models;

use App\StatusPendaftaran;
use Illuminate\Database\Eloquent\Model;

class StatusHistory extends Model
{
    protected $table = 'status_histories';
    protected $fillable = [
        "pendaftaran_id", "status", "changed_at"
    ];

    protected $casts = [
        "status" => StatusPendaftaran::class,
    ];

    public function pendaftaran(){
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id', "id_pendaftaran");
    }
}
