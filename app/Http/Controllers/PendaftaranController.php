<?php

namespace App\Http\Controllers;

use App\Http\Resources\PendaftaranListResource;
use App\Http\Resources\PendaftaranResource;
use App\Models\Lowongan;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendaftaranController extends Controller
{
    public function getDetailPendaftaranByLowonganId(int $lowonganId){
        $user = auth("pelamar")->user();
        $lowongan = Lowongan::find($lowonganId);

        if (!$lowongan) {
            return response()->json(["message" => "Lowongan tidak ditemukan"], 404);
        }
        $pendaftaran = Pendaftaran::where(["lowongan_id"=> $lowonganId, "pelamar_id" => $user->id_pelamar])->first();

        if (!$pendaftaran) {
            return response()->json(["message" => "pendaftaran tidak ditemukan"], 404);
        }

        return new PendaftaranResource($pendaftaran);

    }

    public function userPendaftaran()
    {
        $user = auth("pelamar")->user();
        $user->load("pendaftarans");
        $user->pendaftarans->load("lowongan");
        return  PendaftaranListResource::collection($user->pendaftarans);
    }

    public function userGetPendaftaranById(int $pendaftaranId){
        $user = auth("pelamar")->user();
        $pendaftaran = $user->pendaftarans->find($pendaftaranId);
        if (!$pendaftaran) {
            return response()->json(["message" => "pendaftaran tidak ditemukan"], 404);
        }
        return new PendaftaranResource($pendaftaran);
    }
}
