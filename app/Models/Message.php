<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'message',
        'read_at',
    ];

    // Hubungan polymorphic dengan pengirim
    public function sender()
    {
        return $this->morphTo();
    }

    // Hubungan polymorphic dengan penerima
    public function receiver()
    {
        return $this->morphTo();
    }
}
