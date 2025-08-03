<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatDetail extends JsonResource
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
            "message" => $this->message,
            "read_at" => $this->read_at,
            "sender"=>[
                "id" => $this->sender->id,
                "name" => $this->sender->name,
                "email" => $this->sender->email,
                "type" => class_basename($this->sender),
            ],
            "receiver" => [
                "id" => $this->receiver->id,
                "name" => $this->receiver->name,
                "email" => $this->receiver->email,
                "type" => class_basename($this->receiver),
            ],
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
