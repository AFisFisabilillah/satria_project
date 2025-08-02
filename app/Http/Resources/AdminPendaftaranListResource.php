<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPendaftaranListResource extends JsonResource
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
            "pelamar"=>[
                "id"=>$this->pelamar->id_pelamar,
                "nama"=>$this->pelamar->nama_pelamar,
                "domisili" => $this->pelamar->domisili_pelamar,
                "telp" => $this->pelamar->telp_pelamar,
                "email"=>$this->pelamar->email_pelamar,
                "gender"=>$this->pelamar->kelamin_pelamar,
                "profile"=>asset("storage/".$this->pelamar->profile_pelamar)
            ],
            "lowongan"=>[
                "id"=>$this->lowongan->id_lowongan,
                "nama"=>$this->lowongan->nama_lowongan,
                "negara"=>$this->lowongan->negara_lowongan,
            ],
    
            "submited_at"=>Carbon::parse($this->created_at)->format("d M Y"),
            "status"=>$this->status_pendaftaran->value,
            "followup"=>!is_null($this->cabang_id),

        ];
    }
}
