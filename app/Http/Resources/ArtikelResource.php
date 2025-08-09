<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtikelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
        "id" => $this->id,
        "judul"=>$this->judul,
        "isi" => $this->isi,
        "foto"=>asset("/storage/".$this->foto),
        "kategori" => $this->kategori,
        "tanggal"=>$this->tanggal,
            "is_mobile" => $this->is_mobile,
        "penulis" => $this->penulis_admin ? [
            "id" => $this->penulis_admin->id,
            "nama" => $this->penulis_admin->name_super_admin
        ]:null
        ];
    }
}
