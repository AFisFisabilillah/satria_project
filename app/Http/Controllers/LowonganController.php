<?php

namespace App\Http\Controllers;

use App\Http\Requests\LowonganRequest;
use App\Http\Resources\LowonganPelamarResource;
use App\Http\Resources\LowonganResource;
use App\Http\Resources\LowonganSimpleResource;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LowonganController extends Controller
{
    public function create(LowonganRequest $request)
    {
        $user = auth("admin_cabang")->user();
        $data = $request->validated();
        $lowongan = $user->lowongans()->create([
            "nama_lowongan" => $data["nama"],
            "syarat_lowongan" => $data["syarat"],
            "deskripsi_lowongan" => $data["deskripsi"],
            "posisi_lowongan" => $data["posisi"],
            "gaji_lowongan" => $data["gaji"],
            "deadline_lowongan" => $data["deadline"],
            "negara_lowongan" => $data["negara"],
            "kontrak_lowongan" => $data["kontrak"],
            "lokasi_lowongan" => $data["lokasi"],
            "currency" => $data["currency"],
            "kuota_lowongan" => $data["kuota_lowongan"],
            "status_lowongan" => "OPEN",
        ]);

        return new LowonganResource($lowongan);

    }

    public function getLowonganById(int $lowonganId)
    {
        $user = auth("admin_cabang")->user();
        $lowongan = $user->lowongans()->find($lowonganId);
        if (!$lowongan) {
            return response()->json(["message" => "Lowongan tidak ditemukan"], 404);
        }
        return new LowonganResource($lowongan);
    }

    public function update(LowonganRequest $request, int $lowonganId)
    {
        $data = $request->validated();
        $user = auth("admin_cabang")->user();

        $lowongan = DB::transaction(function () use ($lowonganId, $user, $data) {

            $lowongan = $user->lowongans()->find($lowonganId);

            if (!$lowongan) {
                return response()->json(["message" => "Data lowongan tidak ditemukan"], 404);
            }

            $lowongan->nama_lowongan = $data["nama"];
            $lowongan->syarat_lowongan = $data["syarat"];
            $lowongan->deskripsi_lowongan = $data["deskripsi"];
            $lowongan->posisi_lowongan = $data["posisi"];
            $lowongan->gaji_lowongan = $data["gaji"];
            $lowongan->deadline_lowongan = $data["deadline"];
            $lowongan->negara_lowongan = $data["negara"];
            $lowongan->kontrak_lowongan = $data["kontrak"];
            $lowongan->lokasi_lowongan = $data["lokasi"];
            $lowongan->currency = $data["currency"];
            $lowongan->kuota_lowongan = $data["kuota_lowongan"];
            $lowongan->save();
            return $lowongan;
        });

        return new LowonganResource($lowongan);
    }

    public function delete(int $lowonganId)
    {
        $user = auth("admin_cabang")->user();

        $lowongan = Lowongan::where(["admin_cabang_id" => $user->id, "id_lowongan" => $lowonganId])->first();
        if (!$lowongan) {
            return response()->json(["message" => "Data lowongan tidak ditemukan"], 404);
        }

        $isSuccess = DB::transaction(function () use ($lowongan) {
            return $lowongan->delete();
        });

        if ($isSuccess) {
            return response()->json(["message" => "Data lowongan telah dihapus"], 200);
        } else {
            return response()->json(["message" => "Data lowongan gagal dihapus"], 400);
        }
    }

    function searchLowonganAdminCabang(Request $request)
    {
        $q = $request->get("q");
        $negara = $request->get("negara");
        $size = $request->get("size",10);
        $adminCabangId = auth('admin_cabang')->user()->id; // atau dari $request->query('admin_cabang_id')

        $lowongans = DB::table('lowongans')
            ->when($q, fn($query) => $query->whereFullText(['nama_lowongan', 'deskripsi_lowongan', 'posisi_lowongan'], $q))
            ->when($negara, fn($query) => $query->where('negara_lowongan', $negara))
            ->when($adminCabangId, fn($query) => $query->where('admin_cabang_id', $adminCabangId))
            ->paginate($size);
        return LowonganSimpleResource::collection($lowongans);
    }

//    Unutk user
    function searchLowongan(Request $request)
    {
        $q = $request->get("q");
        $negara = $request->get("negara");
        $posisi = $request->get("posisi",null);
        $maximum = $request->get("maximum");
        $minimum = $request->get("minimum");
        $size = $request->get("size",10);

        $lowongans = DB::table('lowongans')
            ->when($q, fn($query) => $query->whereFullText(['nama_lowongan', 'deskripsi_lowongan', 'posisi_lowongan'], $q))
            ->when($negara, fn($query) => $query->where('negara_lowongan', $negara))
            ->when($minimum !== null && $maximum !== null, function ($query) use ($minimum, $maximum) {
                $query->whereBetween('gaji_lowongan', [$minimum, $maximum]);
            })
            ->when($minimum !== null && $maximum === null, function ($query) use ($minimum) {
                $query->where('gaji_lowongan', '>=', $minimum);
            })
            ->when($maximum !== null && $minimum === null, function ($query) use ($maximum) {
                $query->where('gaji_lowongan', '<=', $maximum);
            })
            ->when($posisi, function ($query) use ($posisi) {
                 $query->whereLike('posisi_lowongan', "%$posisi%");
            })
            ->paginate($size);
        return LowonganSimpleResource::collection($lowongans);
    }

    public function userGetLowonganById(int $lowonganId)
    {
        $lowongan = Lowongan::find($lowonganId);
        if (!$lowongan) {
            return response()->json(["message" => "Data lowongan tidak ditemukan"], 404);
        }
        $user = auth("pelamar")->user();
        $sudahMelamar = $user->pendaftarans->where("lowongan_id", $lowonganId)->isNotEmpty();

        return new LowonganPelamarResource($lowongan,["sudah_melamar"=>$sudahMelamar]);
    }

    public function filterNegara()
    {
        $negara = Lowongan::select("negara_lowongan")->distinct()->pluck("negara_lowongan");
        return response()->json([
            "data"=>$negara
        ]);
    }

    public function filterPosisi(){
        $posisi = Lowongan::select("posisi_lowongan")->distinct()->pluck("posisi_lowongan");
        return response()->json([
            "data"=>$posisi
        ]);
    }

}
