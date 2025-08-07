<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    protected $fillable = ['judul', 'foto', 'isi', 'tanggal', 'kategori', 'penulis'];

    function penulis()
    {
        return $this->belongsTo(SuperAdmin::class, "penulis", "id");
    }
}
