<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PelamarSimpleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            "id"=>$this->id_pelamar,
            "nama"=>$this->nama_pelamar,
            "email"=>$this->email_pelamar,
            "domisili"=>$this->domisili_pelamar,
            "profile"=>asset("/storage/".$this->profile_pelamar),
        ];
    }
}
