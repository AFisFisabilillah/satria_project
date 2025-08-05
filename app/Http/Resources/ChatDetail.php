<?php

namespace App\Http\Resources;

use App\Models\Pelamar;
use App\Models\SuperAdmin;
use App\Models\AdminCabang;
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

        // Mendapatkan data pengirim
        $sender = $this->sender;
        $senderNama = 'Pengguna Tidak Dikenal';
        $senderType = 'Tidak Dikenal';

        if ($sender) {
            $senderType = class_basename(get_class($sender)); // Mengambil hanya nama class (contoh: 'Pelamar')

            // Menyesuaikan nama kolom berdasarkan tipe model
            if ($sender instanceof Pelamar) {
                $senderNama = $sender->nama_pelamar; // Sesuaikan dengan nama kolom nama Pelamar Anda
            } elseif ($sender instanceof AdminCabang) {
                $senderNama = $sender->nama_ac; // Sesuaikan dengan nama kolom nama Admin Cabang Anda
            } elseif ($sender instanceof SuperAdmin) {
                $senderNama = $sender->name_super_admin; // Sesuaikan dengan nama kolom nama Super Admin Anda
            }
        }

        return [
            'id_message' => $this->id,
            'sender_id' => $sender ? ($sender->id ?? $sender->id_pelamar) : null,
            'type' => $senderType,
            'nama' => $senderNama,
            'message' => $this->message,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'), // Atau format yang Anda inginkan
        ];
    }
}
