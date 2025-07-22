<?php

namespace App\Http\Resources;

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
            "lowongan"=> new LowonganSimpleResource($this->lowongan),
            "pendaftaran"=> AdminPendaftaranResource::collection($this)
        ];
    }
}
