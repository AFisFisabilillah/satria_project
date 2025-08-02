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
            "negara"=>$this->negara_lowongan,
            "created_at"=>Carbon::parse($this->created_at)->diffForHumans(),
            "gaji"=>$this->currency." ".number_format($this->min_gaji_lowongan, 0, ',', '.')." - ".number_format($this->max_gaji_lowongan, 0, ',', '.'),
            "syarat"=>$this->syarat_lowongan,
            "kuota_lowongan" => $this->kuota_lowongan,
            "sisa_kuota" => $this->sisa_kuota,
            "sip2mi" => $this->sip2mi,
            "posisi"=>$this->posisi_lowongan
        ];
    }
}
