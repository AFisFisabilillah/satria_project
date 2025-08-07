<?php

namespace App\Http\Controllers;

use App\Http\Requests\LowonganRequest;
use App\Http\Resources\LowonganPelamarResource;
use App\Http\Resources\LowonganResource;
use App\Http\Resources\LowonganSimpleResource;
use App\Models\Lowongan;
use App\Models\Pendaftaran;
use App\Models\StatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LowonganController extends Controller
{
    public function create(LowonganRequest $request)
    {

        $data = $request->validated();
        $lowongan = Lowongan::create([
            "nama_lowongan" => $data["nama"],
            "syarat_lowongan" => $data["syarat"],
            "deskripsi_lowongan" => $data["deskripsi"],
            "posisi_lowongan" => $data["posisi"],
            "max_gaji_lowongan" => $data["max_gaji"],
            "min_gaji_lowongan" => $data["min_gaji"],
            "negara_lowongan" => $data["negara"],
            "currency" => $data["currency"],
            "jumlah_laki" => $data["jumlah_laki"] ?? 0,
            "jumlah_perempuan" => $data["jumlah_perempuan"] ?? 0,
            "kuota_lowongan" => $data["kuota_lowongan"],
            "sip2mi" => $data["sip2mi"],
            "sisakuota" => $data["kuota_lowongan"], // Inisialisasi sisakuota dengan kuota_lowongan
            "batas_waktu" => $data["batas_waktu"],
        ]);

        return new LowonganResource($lowongan);
    }

    public function getLowonganById(int $lowonganId)
    {
        $lowongan = Lowongan::withCount('pendaftarans')->find($lowonganId);
        if (!$lowongan) {
            return response()->json(["message" => "Lowongan tidak ditemukan"], 404);
        }

        $user = auth("pelamar")->user();
        $sudahMelamar = false;
        if ($user) {
            $sudahMelamar = $user->pendaftarans->where("lowongan_id", $lowonganId)->isNotEmpty();
        }

        // Hitung sisa kuota
        $sisaKuota = $lowongan->kuota_lowongan - $lowongan->pendaftarans_count;

        return (new LowonganPelamarResource($lowongan))
            ->additional([
                "sudah_melamar" => $sudahMelamar,
                "sisa_kuota" => max($sisaKuota, 0)
            ]);
    }

    public function update(LowonganRequest $request, int $lowonganId)
    {

        $data = $request->validated();
        $lowongan = DB::transaction(function () use ($lowonganId, $data) {

            $lowongan = Lowongan::find($lowonganId);

            if (!$lowongan) {
                return response()->json(["message" => "Data lowongan tidak ditemukan"], 404);
            }

            $sisakuota = ; // Ambil nilai sisakuota yang sudah ada

            $kuotaLowongan = $data["jumlah_laki"] + $data["jumlah_perempuan"];

            $lowongan->nama_lowongan = $data["nama"];
            $lowongan->syarat_lowongan = $data["syarat"];
            $lowongan->deskripsi_lowongan = $data["deskripsi"];
            $lowongan->posisi_lowongan = $data["posisi"];
            $lowongan->max_gaji_lowongan = $data["max_gaji"];
            $lowongan->min_gaji_lowongan = $data["min_gaji"];
            $lowongan->batas_waktu = $data["batas_waktu"];
            $lowongan->negara_lowongan = $data["negara"];
            $lowongan->jumlah_laki = $data["jumlah_laki"] ?? 0;
            $lowongan->jumlah_perempuan = $data["jumlah_perempuan"] ?? 0;
            $lowongan->currency = $data["currency"];
            $lowongan->kuota_lowongan = $kuotaLowongan;
            $lowongan->sip2mi = $data["sip2mi"];
            $lowongan->save();
            return $lowongan;
        });

        return new LowonganResource($lowongan);
    }

    public function delete(int $lowonganId)
    {

        $lowongan = Lowongan::find($lowonganId);
        if (!$lowongan) {
            return response()->json(["message" => "Data lowongan tidak ditemukan"], 404);
        }

        $isSuccess = DB::transaction(function () use ($lowongan) {
            // Ambil semua pendaftaran_id berdasarkan lowongan_id
            $pendaftaranIds = Pendaftaran::where('lowongan_id', $lowongan->id_lowongan)->pluck('id_pendaftaran');
            $statusDelete = StatusHistory::whereIn('pendaftaran_id', $pendaftaranIds)->delete();
            $pendaftarandelete  = Pendaftaran::whereIn('id_pendaftaran', $pendaftaranIds)->delete();
            return $lowongan->delete();
        });

        if ($isSuccess) {
            return response()->json(["message" => "Data lowongan telah dihapus"], 200);
        } else {
            return response()->json(["message" => "Data lowongan gagal dihapus"], 400);
        }
    }


    //    Unutk user
    function searchLowongan(Request $request)
    {
        $q = $request->get("q");
        $negara = $request->get("negara");
        $posisi = $request->get("posisi", null);
        $maximum = $request->get("maximum");
        $minimum = $request->get("minimum");
        $size = $request->get("size", 10);

        $lowongans = DB::table('lowongans')
            ->leftJoin('pendaftarans', 'lowongans.id_lowongan', '=', 'pendaftarans.lowongan_id')
            ->select(
                'lowongans.*',
                DB::raw('COUNT(pendaftarans.id_pendaftaran) as jumlah_pendaftar'),
                DB::raw('(lowongans.kuota_lowongan - COUNT(pendaftarans.id_pendaftaran)) as sisa_kuota')
            )
            ->when(
                $q,
                fn($query) =>
                $query->whereFullText(['nama_lowongan', 'deskripsi_lowongan', 'posisi_lowongan'], $q)
            )
            ->when(
                $negara,
                fn($query) =>
                $query->where('negara_lowongan', $negara)
            )
            ->when($minimum !== null && $maximum !== null, function ($query) use ($minimum, $maximum) {
                $query->where('min_gaji_lowongan', '<=', $maximum)
                    ->where('max_gaji_lowongan', '>=', $minimum);
            })
            ->when($minimum !== null && $maximum === null, function ($query) use ($minimum) {
                $query->where('max_gaji_lowongan', '>=', $minimum);
            })
            ->when($maximum !== null && $minimum === null, function ($query) use ($maximum) {
                $query->where('min_gaji_lowongan', '<=', $maximum);
            })
            ->when($posisi, function ($query) use ($posisi) {
                $query->where('posisi_lowongan', 'like', "%$posisi%");
            })
            ->whereNull('lowongans.deleted_at')
            ->groupBy('lowongans.id_lowongan') // wajib saat pakai COUNT
            ->orderBy("lowongans.created_at", "desc")
            ->paginate($size);


        return LowonganSimpleResource::collection($lowongans);
    }



    public function filterNegara()
    {
        $negara = Lowongan::select("negara_lowongan")->distinct()->pluck("negara_lowongan");
        return response()->json([
            "data" => $negara
        ]);
    }

    public function filterPosisi()
    {
        $posisi = Lowongan::select("posisi_lowongan")->distinct()->pluck("posisi_lowongan");
        return response()->json([
            "data" => $posisi
        ]);
    }

    /*
 * TODO
 * 1. Fixed delete lowongan
 * 2.

*/
}
