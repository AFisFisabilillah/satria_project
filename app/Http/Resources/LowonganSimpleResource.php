<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LowonganSimpleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id_lowongan,
            "nama"=>$this->nama_lowongan,
            "lokasi"=>$this->lokasi_lowongan,
            "negara"=>$this->negara_lowongan,
            "created_at"=>Carbon::parse($this->created_at)->diffForHumans(),
            "gaji"=>$this->currency." ".$this->gaji_lowongan,
            "syarat"=>$this->syarat_lowongan
        ];
    }
}
