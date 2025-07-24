<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDirekturResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "nama"=>$this->nama_direktur,
            "jabatan"=>$this->jabatan_direktur,
            "telp"=>$this->telp_direktur,
            "email"=>$this->email_direktur
        ];
    }
}
