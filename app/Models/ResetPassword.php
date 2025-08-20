<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    protected $fillable = ["pelamar_id", "token", "expired_code", "is_used", "is_valid"];

    public function pelamar(){
        return $this->belongsTo(Pelamar::class, "pelamar_id", "id_pelamar");
    }
}
