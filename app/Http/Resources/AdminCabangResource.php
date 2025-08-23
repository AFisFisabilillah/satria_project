<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminCabangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "nama"=>$this->nama_ac,
            "email"=>$this->email_ac,
            "telp"=>$this->telp_ac,
            "profile" => asset("storage/" . $this->photo_profile),
            "cabang"=>$this->cabang == null ? null : [
                "id" => $this->cabang->id_cabang,
                "nama"=>$this->cabang->nama_cabang,
            ],
        ];
    }
}
