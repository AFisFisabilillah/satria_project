<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artikel extends Model
{
    protected $fillable = ['judul', 'foto', 'isi', 'tanggal', 'kategori', 'penulis', "is_mobile"];

    function penulis_admin() :BelongsTo
    {
        return $this->belongsTo(SuperAdmin::class, "penulis", "id");
    }
}
