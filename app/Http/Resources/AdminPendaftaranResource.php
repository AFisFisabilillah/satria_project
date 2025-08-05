<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPendaftaranResource extends JsonResource
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
                "profile"=>asset("/storage/".$this->pelamar->profile_pelamar),
                "nama"=>$this->pelamar->nama_pelamar,
                "email"=>$this->pelamar->email_pelamar,
                "gender"=>$this->pelamar->kelamin_pelamar,
                "domisili"=>$this->pelamar->domisili_pelamar,
                "umur"=>Carbon::parse($this->pelamar->ttl_pelamar)->age
            ],
            "reviewed_by" => [
                "id" => $this->riviewed_by_id,
                "nama" => $this->riviewed_by->nama_ac ?? $this->riviewed_by->name_super_admin ?? "null" ,
            ] ,            "waktu_pendaftaran" => Carbon::parse($this->waktu_pendaftaran)->format('Y-m-d'),
            "status"=>$this->status_pendaftaran->value,
            "deskripsi_status"=>$this->status_pendaftaran->label()
        ];
    }
}
