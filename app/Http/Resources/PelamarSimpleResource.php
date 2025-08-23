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
        return [
            "id" => $this->id_pelamar,
            "nama" => $this->nama_pelamar,
            "email" => $this->email_pelamar,
            "domisili" => $this->domisili_pelamar,
            "gender" => $this->kelamin_pelamar,
            "profile" => asset("/storage/" . $this->profile_pelamar),
            "type" => $this->type,
            "admin" => $this->admin ? [
                "id"   => $this->admin->id, // Akses langsung dari objek admin
                "type" => $this->admin_type,
                "email" => $this->admin->email_super_admin ?? $this->admin->email_ac, // Menggunakan email sesuai tipe admin
                "nama" => $this->admin->name_super_admin ?? $this->admin->nama_ac, // Menggunakan nama sesuai tipe admin
            ] : null,
        ];
    }
}
