<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminPendaftaranListResource;
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
    public function getDetailPendaftaranByLowonganId(int $lowonganId)
    {
        $user = auth("pelamar")->user();
        $lowongan = Lowongan::find($lowonganId);

        if (!$lowongan) {
            return response()->json(["message" => "Lowongan tidak ditemukan"], 404);
        }
        $pendaftaran = Pendaftaran::where(["lowongan_id" => $lowonganId, "pelamar_id" => $user->id_pelamar])->first();

        if (!$pendaftaran) {
            return response()->json(["message" => "pendaftaran tidak ditemukan"], 404);
        }

        return new PendaftaranResource($pendaftaran);
    }

    public function getAll(Request $request)
    {
        $domisili = $request->get("domisili");
        $size = $request->get("size", 10);
        $gender = $request->get("gender", null);
        $q = $request->get("q", null);

        $pendaftarans = Pendaftaran::when($domisili, function ($query) use ($domisili) {
            $query->whereHas("pelamar", function ($query) use ($domisili) {
                $query->where("domisili_pelamar", $domisili);
            });
        })->when($gender, function ($query) use ($gender) {
            $query->whereHas("pelamar", function ($query) use ($gender) {
                $query->where("kelamin_pelamar", $gender);
            });
        })->when($q, function ($query) use ($q) {
            $query->whereHas("pelamar", function ($query) use ($q) {
                $query->whereFullText(["nama_pelamar", "email_pelamar"], $q);
            });
        })->orderBy("created_at", "asc")->with(["pelamar", "lowongan"])->paginate($size);


        return  AdminPendaftaranListResource::collection($pendaftarans);
    }

    public function userPendaftaran()
    {
        $user = auth("pelamar")->user();
        $user->load("pendaftarans");
        $user->pendaftarans->load("lowongan");
        return  PendaftaranListResource::collection($user->pendaftarans);
    }

    public function userGetPendaftaranById(int $pendaftaranId)
    {
        $user = auth("pelamar")->user();
        $pendaftaran = $user->pendaftarans->find($pendaftaranId);
        if (!$pendaftaran) {
            return response()->json(["message" => "pendaftaran tidak ditemukan"], 404);
        }
        return new PendaftaranResource($pendaftaran);
    }

    public function getAllPendaftaranByLowonganId(int $lowonganId, Request $request)
    {
        $gender = $request->get('gender');
        $domisili = $request->get('domisili');
        $q = $request->get('q');

        $lowongan = Lowongan::find($lowonganId);

        if (!$lowongan) {
            return response()->json([
                'message' => 'Lowongan tidak ditemukan',
            ], 404);
        }

        $pendaftarans = $lowongan->pendaftarans()
            ->whereHas('pelamar', function ($query) use ($gender, $domisili, $q) {
                if ($gender) {
                    $query->where('kelamin_pelamar', $gender);
                }
                if ($domisili) {
                    $query->where('domisili_pelamar', $domisili);
                }
                if ($q) {
                    $query->whereFullText(['nama_pelamar', 'email_pelamar'], $q);
                }
                $query->whereNull('cabang_id');
            })
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

    public function getAllPendaftaran(Request $request){


        $domisili = $request->get("domisili");
        $size = $request->get("size", 10);
        $gender = $request->get("gender", null);
        $status = $request->get("status", null);
        $q = $request->get("q", null);
        $waktu = $request->get("waktu");
        $followup = $request->get("followup", null);



        $pendaftarans = Pendaftaran::when($domisili, function ($query) use ($domisili) {
            $query->whereHas("pelamar", function ($query) use ($domisili) {
                $query->where("domisili_pelamar", $domisili);
            });
        })->when($gender, function ($query) use ($gender) {
            $query->whereHas("pelamar", function ($query) use ($gender) {
                $query->where("kelamin_pelamar", $gender);
            });
        })->when($q, function ($query) use ($q) {
            $query->whereHas("pelamar", function ($query) use ($q) {
                $query->whereFullText(["nama_pelamar", "email_pelamar"], $q);
            });
        })->when($status, function ($query) use ($status) {
            $query->where("status_pendaftaran", $status);
        })->when($waktu, function ($query) use ($waktu) {
            $query->whereDate("waktu_pendaftaran", $waktu);
        })->when($followup, function ($query) use ($followup) {
            if($followup === "true") {
                $query->whereNotNull("cabang_id");
            }
        })->paginate($size);

        return AdminPendaftaranListResource::collection($pendaftarans);
    }

    public function followup(int $pendaftaranId)
    {
        $pendaftaran = Pendaftaran::find($pendaftaranId);
        $adminCabang = auth("admin_cabang")->user();

        if (!$pendaftaran) {
            return response()->json([
                "message" => "Pendaftaran tidak ditemukan"
            ], 404);
        }

        $pendaftaran->cabang_id = $adminCabang->cabang_id;
        $pendaftaran->save();

        return response()->json([
            "message" => "Pendaftaran dengan id $pendaftaranId di cabang id " . $adminCabang->cabang_id
        ], 200);
    }

    public function changeStatusToRiviewedByHrd(int $pendaftaranId)
    {

        $pendaftaran = null;

        if (Auth::guard("admin_cabang")->check()) {

            $adminCabang = auth("admin_cabang")->user();
            $pendaftaran = $adminCabang->cabang->pendaftarans()->find($pendaftaranId);
        } elseif (Auth::guard("super_admin")->check()) {

            $pendaftaran = Pendaftaran::find($pendaftaranId);
        }


        if (!$pendaftaran) {
            return response()->json([
                "message" => "Pendaftaran tidak ditemukan"
            ], 404);
        }


        $pendaftaran->load("pelamar");

        if ($pendaftaran->status_pendaftaran->value !== StatusPendaftaran::Submitted->value) {
            return response()->json([
                "message" => "Status pendaftaran harus 'dikirim terlebih dahulu ' terlebih dahulu sebelum aksi ini dapat dilakukan."
            ], 400);
        }

        $pendaftaran->status_pendaftaran = StatusPendaftaran::ReviewedByHR->value;
        $pendaftaran->save();

        StatusHistory::create([
            "pendaftaran_id" => $pendaftaran->id_pendaftaran,
            "status" => StatusPendaftaran::Interview->value,
        ]);
        return new AdminPendaftaranResource($pendaftaran);
    }

    public function changeStatusToInterview(int $pendaftaranId)
    {
        $pendaftaran = null;

        if (Auth::guard("admin_cabang")->check()) {

            $adminCabang = auth("admin_cabang")->user();
            $pendaftaran = $adminCabang->cabang->pendaftarans()->find($pendaftaranId);
        } elseif (Auth::guard("super_admin")->check()) {

            $pendaftaran = Pendaftaran::find($pendaftaranId);
        }


        if (!$pendaftaran) {
            return response()->json([
                "message" => "Pendaftaran tidak ditemukan"
            ], 404);
        }


        $pendaftaran->load("pelamar");

        if ($pendaftaran->status_pendaftaran->value !== StatusPendaftaran::ReviewedByHR->value) {
            return response()->json([
                "message" => "Status pendaftaran harus 'reviewed by HR' terlebih dahulu sebelum aksi ini dapat dilakukan."
            ], 400);
        }

        $pendaftaran->status_pendaftaran = StatusPendaftaran::Interview->value;
        $pendaftaran->save();

        StatusHistory::create([
            "pendaftaran_id" => $pendaftaran->id_pendaftaran,
            "status" => StatusPendaftaran::Interview->value,
        ]);
        return new AdminPendaftaranResource($pendaftaran);
    }

    public function changeStatusToAccepted(int $pendaftaranId)
    {
        $pendaftaran = null;

        if (Auth::guard("admin_cabang")->check()) {

            $adminCabang = auth("admin_cabang")->user();
            $pendaftaran = $adminCabang->cabang->pendaftarans()->find($pendaftaranId);
        } elseif (Auth::guard("super_admin")->check()) {
            $pendaftaran = Pendaftaran::find($pendaftaranId);
        }

        if (!$pendaftaran) {
            return response()->json([
                "message" => "Pendaftaran tidak ditemukan"
            ], 404);
        }

        $pendaftaran->load("pelamar");

        if ($pendaftaran->status_pendaftaran->value !== StatusPendaftaran::Interview->value) {
            return response()->json([
                "message" => "Status pendaftaran harus 'Interview' terlebih dahulu sebelum aksi ini dapat dilakukan."
            ], 400);
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

    public function changeStatusToReject(int $pendaftaranId)
    {
        $pendaftaran = null;

        if (Auth::guard("admin_cabang")->check()) {

            $adminCabang = auth("admin_cabang")->user();
            $pendaftaran = $adminCabang->cabang->pendaftarans()->find($pendaftaranId);
        } elseif (Auth::guard("super_admin")->check()) {

            $pendaftaran = Pendaftaran::find($pendaftaranId);
        }

        if (!$pendaftaran) {
            return response()->json([
                "message" => "Pendaftaran tidak ditemukan"
            ], 404);
        }

        $pendaftaran->load("pelamar");

        if ($pendaftaran->status_pendaftaran->value === StatusPendaftaran::Accepted->value) {
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

    public function getByCabang(Request $request)
    {
        $adminCabang = auth("admin_cabang")->user();
        $domisili = $request->get("domisili");
        $size = $request->get("size", 10);
        $gender = $request->get("gender", null);
        $status = $request->get("status", null);
        $q = $request->get("q", null);
        $waktu = $request->get("waktu");



        $pendaftarans = Pendaftaran::when($domisili, function ($query) use ($domisili) {
            $query->whereHas("pelamar", function ($query) use ($domisili) {
                $query->where("domisili_pelamar", $domisili);
            });
        })->when($gender, function ($query) use ($gender) {
            $query->whereHas("pelamar", function ($query) use ($gender) {
                $query->where("kelamin_pelamar", $gender);
            });
        })->when($q, function ($query) use ($q) {
            $query->whereHas("pelamar", function ($query) use ($q) {
                $query->whereFullText(["nama_pelamar", "email_pelamar"], $q);
            });
        })->when($status, function ($query) use ($status) {
            $query->where("status_pendaftaran", $status);
        })->when($waktu, function ($query) use ($waktu) {
            $query->whereDate("waktu_pendaftaran", $waktu);
        })
            ->where("pendaftarans.cabang_id", $adminCabang->cabang_id)->orderBy("created_at", "asc")->with(["pelamar", "lowongan"])->paginate($size);

        return AdminPendaftaranListResource::collection($pendaftarans);
    }



    public function superAdminGetPendaftaranByLowonganId(int $lowonganId, Request $request)
    {
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
