<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuperAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "nama"=>$this->name_super_admin,
            "email"=>$this->email_super_admin,
            "profile"=>asset("storage/" . $this->photo_profile),
        ];
    }
}
