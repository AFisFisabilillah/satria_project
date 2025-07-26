<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminPendaftaranResource;
use App\Http\Resources\LowonganResource;
use App\Http\Resources\LowonganSimpleResource;
use App\Http\Resources\PendaftaranListResource;
use App\Http\Resources\PendaftaranResource;
use App\Models\Lowongan;
use App\Models\Pendaftaran;
use App\Models\StatusHistory;
use App\StatusPendaftaran;
use http\Env\Response;
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

    public function getAllPendaftaranByLowonganId(int $lowonganId,Request $request, )
    {

        $lowongan = Pendaftaran::find($lowonganId);

        if (!$lowongan) {
            return response()->json([
                'message' => 'Lowongan tidak ditemukan',
            ], 404);
        }

        $pendaftarans = $lowongan->pendaftarans()
            ->with(['pelamar'])
            ->paginate($request->get('size', 10));

        return response()->json([
            'data' => [
                'lowongan' => new LowonganSimpleResource($lowongan),
                'pendaftarans' => AdminPendaftaranResource::collection($pendaftarans->items()),
            ],
            'links' => [
                'first' => $pendaftarans->url(1),
                'last' => $pendaftarans->url($pendaftarans->lastPage()),
                'prev' => $pendaftarans->previousPageUrl(),
                'next' => $pendaftarans->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $pendaftarans->currentPage(),
                'from' => $pendaftarans->firstItem(),
                'last_page' => $pendaftarans->lastPage(),
                'path' => $request->url(),
                'per_page' => $pendaftarans->perPage(),
                'to' => $pendaftarans->lastItem(),
                'total' => $pendaftarans->total(),
            ]
        ]);
    }
    public function changeStatusToRiviewedByHrd( int $lowonganId, int $pendaftaranId )
    {
        $adminCabang = auth("admin_cabang")->user();

        $lowongan = $adminCabang->lowongans->find($lowonganId);

        if (!$lowongan) {
            return response()->json([
                'message' => 'Lowongan tidak ditemukan',
            ], 404);
        }

        $pendaftaran = $lowongan->pendaftarans()->find($pendaftaranId)->load("pelamar");
        if (!$pendaftaran) {
            return response()->json(["message" => "pendaftaran tidak ditemukan"], 404);
        }

        if($pendaftaran->status_pendaftaran->value !== StatusPendaftaran::Submitted->value){
            return response()->json(["message" => "status Pendaftaran harus submit terlebih dahulu"], 404);
        }

        $pendaftaran->status_pendaftaran = StatusPendaftaran::ReviewedByHR->value;
        $pendaftaran->save();

        StatusHistory::create([
            "pendaftaran_id" => $pendaftaranId,
            "status" => StatusPendaftaran::ReviewedByHR->value,
        ]);

        return new AdminPendaftaranResource($pendaftaran);
    }

    public function changeStatusToInterview(int $lowonganId, int $pendaftaranId )
    {
        $adminCabang = auth("admin_cabang")->user();

        $lowongan = $adminCabang->lowongans->find($lowonganId);

        if (!$lowongan) {
            return response()->json([
                'message' => 'Lowongan tidak ditemukan',
            ], 404);
        }

        $pendaftaran = $lowongan->pendaftarans()->find($pendaftaranId)->load("pelamar");
        if (!$pendaftaran) {
            return response()->json(["message" => "pendaftaran tidak ditemukan"], 404);
        }

        if($pendaftaran->status_pendaftaran->value !== StatusPendaftaran::ReviewedByHR->value){
            return response()->json(["message" => "Status pendaftaran harus 'reviewed by HR' terlebih dahulu sebelum aksi ini dapat dilakukan."
            ], 404);
        }

//        Mengubah status pendaftaran menjadi interview
        $pendaftaran->status_pendaftaran = StatusPendaftaran::Interview->value;
        $pendaftaran->save();

        StatusHistory::create([
            "pendaftaran_id" => $pendaftaranId,
            "status" => StatusPendaftaran::Interview->value,
        ]);


        return new AdminPendaftaranResource($pendaftaran);
    }

    public function changeStatusToAccepted(int $lowonganId, int $pendaftaranId )
    {
        $adminCabang = auth("admin_cabang")->user();

        $lowongan = $adminCabang->lowongans->find($lowonganId);

        if (!$lowongan) {
            return response()->json([
                'message' => 'Lowongan tidak ditemukan',
            ], 404);
        }

        $pendaftaran = $lowongan->pendaftarans()->find($pendaftaranId)->load("pelamar");
        if (!$pendaftaran) {
            return response()->json(["message" => "pendaftaran tidak ditemukan"], 404);
        }

        if($pendaftaran->status_pendaftaran->value !== StatusPendaftaran::Interview->value){
            return response()->json(["message" => "Status pendaftaran harus 'Interview' terlebih dahulu sebelum aksi ini dapat dilakukan."
            ], 404);
        }

//        Mengubah status pendaftaran menjadi interview
        $pendaftaran->status_pendaftaran = StatusPendaftaran::Accepted->value;
        $pendaftaran->save();

        StatusHistory::create([
            "pendaftaran_id" => $pendaftaranId,
            "status" => StatusPendaftaran::Accepted->value,
        ]);

        return new AdminPendaftaranResource($pendaftaran);
    }

    public function changeStatusToReject(int $lowonganId, int $pendaftaranId )
    {
        $adminCabang = auth("admin_cabang")->user();

        $lowongan = $adminCabang->lowongans->find($lowonganId);

        if (!$lowongan) {
            return response()->json([
                'message' => 'Lowongan tidak ditemukan',
            ], 404);
        }

        $pendaftaran = $lowongan->pendaftarans()->find($pendaftaranId)->load("pelamar");
        if (!$pendaftaran) {
            return response()->json(["message" => "pendaftaran tidak ditemukan"], 404);
        }

        if($pendaftaran->status_pendaftaran->value === StatusPendaftaran::Accepted->value){
            return response()->json(["message" => "pelamar sudah diterima jadi tidak bisa di tolak"], 400);
        }


//        Mengubah status pendaftaran menjadi interview
        $pendaftaran->status_pendaftaran = StatusPendaftaran::Rejected->value;
        $pendaftaran->save();

        StatusHistory::create([
            "pendaftaran_id" => $pendaftaranId,
            "status" => StatusPendaftaran::Rejected->value,
        ]);

        return new AdminPendaftaranResource($pendaftaran);
    }

    public function superAdminGetPendaftaranByLowonganId(int $lowonganId,Request $request){
        $lowongan = Lowongan::find($lowonganId);
        if (!$lowongan) {
            return response()->json(["message" => "Lowongan tidak ditemukan"], 404);
        }

        $pendaftarans = $lowongan->pendaftarans()
            ->with(['pelamar'])
            ->paginate($request->get('size', 10));

        return response()->json([
            'data' => [
                'lowongan' => new LowonganSimpleResource($lowongan),
                'pendaftarans' => AdminPendaftaranResource::collection($pendaftarans->items()),
            ],
            'links' => [
                'first' => $pendaftarans->url(1),
                'last' => $pendaftarans->url($pendaftarans->lastPage()),
                'prev' => $pendaftarans->previousPageUrl(),
                'next' => $pendaftarans->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $pendaftarans->currentPage(),
                'from' => $pendaftarans->firstItem(),
                'last_page' => $pendaftarans->lastPage(),
                'path' => $request->url(),
                'per_page' => $pendaftarans->perPage(),
                'to' => $pendaftarans->lastItem(),
                'total' => $pendaftarans->total(),
            ]
        ]);
    }

}
