<?php

namespace App\Http\Controllers;

use App\Http\Requests\CabangRequest;
use App\Http\Resources\CabangCollection;
use App\Http\Resources\CabangResource;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabangController extends Controller
{
    public function create(CabangRequest $request)
    {
        $data = $request->validated();

        try {
            $cabang = DB::transaction(function () use ($data) {
                return Cabang::create([
                    'nama_cabang' => $data['nama'],
                    "alamat_cabang" => $data['alamat'],
                    "kota_cabang" => $data['kota'],
                    "kepala_cabang" => $data['kepala_cabang'],
                ]);
            });
            return new CabangResource($cabang);
        } catch (\Throwable $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "status" => "error"
            ], 500);
        }
    }

    public function getCabang(Request $request)
    {
        $query = $request->query("q", null);
        $data = null;
        if ($query != null) {
            $data = Cabang::where("nama_cabang", "LIKE", "%$query%")
                ->orderBy("created_at", "desc")
                ->get();
        } else {
            $data = Cabang::latest()->get(); // sama dengan orderBy('created_at', 'desc')
        }

        return new CabangCollection($data);
    }

    public function update(CabangRequest $request, int $cabangId)
    {
        $data = $request->validated();
        $cabang = Cabang::find($cabangId);
        if (!$cabang) {
            return response()->json([
                "message" => "cabang not found",
                "status" => "error"
            ], 404);
        }
        try {
            $cabangUpdate = DB::transaction(function () use ($data, $cabang) {
                $cabang->update([
                    'nama_cabang' => $data['nama'],
                    "alamat_cabang" => $data['alamat'],
                    "kota_cabang" => $data['kota'],
                    "kepala_cabang" => $data['kepala_cabang'],
                ]);

                return $cabang;
            });
            return new CabangResource($cabangUpdate);
        } catch (\Throwable $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "status" => "error"
            ], 500);
        }
    }

    public function getCabangById(int $cabangId)
    {
        $cabang = Cabang::find($cabangId);
        if (!$cabang) {
            return response()->json([
                "message" => "cabang not found",
                "status" => "error"
            ], 404);
        }
        return new CabangResource($cabang);
    }

    public function delete(int $cabangId)
    {
        $cabang = Cabang::find($cabangId);
        if (!$cabang) {
            return response()->json([
                "message" => "cabang not found",
                "status" => "error"
            ], 404);
        }

        $cabang->delete();
        return response()->json([
            "message" => "cabang deleted successfully",
            "status" => "success"
        ]);
    }
}
