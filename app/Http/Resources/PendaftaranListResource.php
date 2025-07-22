<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendaftaranListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id_pendaftaran,
            "nama_lowongan"=>$this->lowongan->nama_lowongan,
            'waktu_pendaftaran' => $this->waktu_pendaftaran,
            'status'=>$this->status_pendaftaran,
        ];
    }
}
