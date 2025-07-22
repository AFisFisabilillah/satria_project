<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "label"=>$this->status->label(),
            "status"=>$this->status->value,
            "time"=>Carbon::parse($this->change_at)->format('Y-m-d H:i')
        ];
    }
}
