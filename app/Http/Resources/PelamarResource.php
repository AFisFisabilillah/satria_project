<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PelamarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name"=>$this->nama_pelamar,
            "email"=>$this->email_pelamar,
            "telp"=>$this->telp_pelamar,
            "domisili"=>$this->domisili_pelamar,
            "tanggal_lahir"=>$this->ttl_pelamar,
            "status_nikah"=>$this->status_nikah_pelamar,
            "jenis_kelamin"=>$this->kelamin_pelamar,
            "profile"=>asset("/storage/".$this->profile_pelamar),
            "ktp" => asset("/storage/".$this->ktp_pelamar)
        ];
    }
}
