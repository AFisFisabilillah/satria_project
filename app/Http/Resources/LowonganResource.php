<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LowonganResource extends JsonResource
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
            "deskripsi"=>$this->deskripsi_lowongan,
            "posisi"=>$this->posisi_lowongan,
            "gaji"=>$this->gaji_lowongan,
            "deadline"=>$this->deadline_lowongan,
            "kontrak"=>$this->kontrak_lowongan,
            "currency"=>$this->currency,
            "jumlah_lowongan"=>$this->jumlah_lowongan,
            "status_lowongan"=>$this->status_lowongan,
            "lokasi"=>$this->lokasi_lowongan,
            "created_at"=>Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }

}
