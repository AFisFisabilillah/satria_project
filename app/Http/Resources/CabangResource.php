<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CabangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id_cabang,
            "nama"=>$this->nama_cabang,
            "alamat"=>$this->alamat_cabang,
            "kota"=>$this->kota_cabang,
            "kepala_cabang"=>$this->kepala_cabang
        ];
    }
}
