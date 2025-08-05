<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatListPelamarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       $pelamar = null;

    if ($this->sender_type === \App\Models\Pelamar::class) {
        $pelamar = $this->sender;
    } elseif ($this->receiver_type === \App\Models\Pelamar::class) {
        $pelamar = $this->receiver;
    }

    return [
        'id' => $pelamar->id_pelamar ?? null,
        'type' => 'Pelamar',
        'nama' => $pelamar->nama_pelamar ?? null,
        'profile' => $pelamar->profile_pelamar ?? null,
        'email' => $pelamar->email_pelamar ?? null,
        'terakhir_chat' => $this->message,
        'created_at' => $this->created_at,
    ];
    }
}
