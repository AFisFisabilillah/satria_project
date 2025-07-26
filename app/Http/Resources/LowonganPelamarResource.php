<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LowonganPelamarResource extends JsonResource
{
    protected $addtionalData;
    public function __construct($resource, $addtionalData = [])
    {
        parent::__construct($resource);
        $this->addtionalData = $addtionalData;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return ["id"=>$this->id_lowongan,
            "nama"=>$this->nama_lowongan,
            "deskripsi"=>$this->deskripsi_lowongan,
            "syarat"=>$this->syarat_lowongan,
            "posisi"=>$this->posisi_lowongan,
            "gaji"=>$this->currency." ".number_format($this->min_gaji_lowongan, 0, ',', '.')." - ".number_format($this->max_gaji_lowongan, 0, ',', '.'),
            "batas_waktu"=>$this->batas_waktu,
            "currency"=>$this->currency,
            "kuota_lowongan"=>$this->kuota_lowongan,
            "created_at"=>Carbon::parse($this->created_at)->diffForHumans(),
            "created"=>Carbon::parse($this->created_at)->format('d M Y'),
            "sudah_melamar"=>$this->addtionalData["sudah_melamar"],
            "sip2mi"=>$this->sip2mi
            ];
    }


}
