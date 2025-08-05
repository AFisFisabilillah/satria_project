<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\AdminCabang;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AdminCabangRequest;
use App\Http\Resources\AdminCabangResource;
use App\Http\Requests\AdminCabangLoginRequest;
use App\Http\Requests\AdminCabangUpdateRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminCabangController extends Controller
{

    public function update_profile(AdminCabangUpdateRequest $request){
        $adminCabang = auth("admin_cabang")->user();
         
        $data =  $request->validated();

    
        if (!$adminCabang) {
            return response()->json(["message" => "Admin Cabang Tidak Ditemukan"], 404);
        }

        $profile = $request->file("profile");
        if ($profile) {
            // generate nama file unik
            $profileName = Str::uuid()->toString() . '.' . $profile->getClientOriginalExtension();
            $profilePath = $profile->storeAs('profiles', $profileName, 'public');
            $data['profile'] = $profilePath;

            // hapus file lama jika ada
            if ($adminCabang->photo_profile && Storage::disk('public')->exists($adminCabang->photo_profile)) {
                Storage::disk('public')->delete($adminCabang->photo_profile);
            }

            $adminCabang->photo_profile = $data['profile'];
        }

        if (!empty($data['password'])) {
            $adminCabang->password_ac = Hash::make($data['password']);
        }

        $adminCabang->nama_ac = $data['nama'];
        $adminCabang->telp_ac = $data['telp'];

        $adminCabang->save();

        return new AdminCabangResource($adminCabang);
    
    }
    

    public function create(AdminCabangRequest $request, int $cabangId)
    {
        $data = $request->validated();
        $profile = $request->file("profile");

        if ($profile) {
            $profileName = Str::uuid()->toString() . '.' . $profile->getClientOriginalExtension();
            $profilePath = $profile->storeAs('profiles', $profileName, 'public');
            $data['profile'] = $profilePath;
        }
        $cabang = Cabang::find($cabangId);

        if (!$cabang) {
            throw new NotFoundHttpException("Cabang Tidak Ditemukan");
        }

        $adminCabang = $cabang->adminCabangs()->create([
            "nama_ac" => $data['nama'],
            "email_ac" => $data['email'],
            "telp_ac" => $data['telp'],
            "photo_profile" => $data['profile'] ?? null,
            "password_ac" => Hash::make($data['password']),
        ]);

        $adminCabang->cabang = $cabang;
        return new AdmincabangResource($adminCabang);
    }

    public function getAllAdminCabangs(Request $request, int $cabangId)
    {
        $size = $request->query("size", 10);
        $adminCabangs = AdminCabang::with("cabang")->where('cabang_id', $cabangId)->paginate($size);
        return AdminCabangResource::collection($adminCabangs);
    }

    public function update(AdminCabangUpdateRequest $request, $cabangId, $adminCabangId)
    {
        $data =  $request->validated();
        $cabang = Cabang::find($cabangId);

        if (!$cabang) {
            return response()->json(["message" => "Cabang Tidak Ditemukan"], 404);
        }

        $adminCabang = AdminCabang::where(["cabang_id" => $cabangId, "id" => $adminCabangId])->first();

        if (!$adminCabang) {
            return response()->json(["message" => "Admin Cabang Tidak Ditemukan"], 404);
        }

        $profile = $request->file("profile");
        if ($profile) {
            // generate nama file unik
            $profileName = Str::uuid()->toString() . '.' . $profile->getClientOriginalExtension();
            $profilePath = $profile->storeAs('profiles', $profileName, 'public');
            $data['profile'] = $profilePath;

            // hapus file lama jika ada
            if ($adminCabang->photo_profile && Storage::disk('public')->exists($adminCabang->photo_profile)) {
                Storage::disk('public')->delete($adminCabang->photo_profile);
            }

            $adminCabang->photo_profile = $data['profile'];
        }

        if (!empty($data['password'])) {
            $adminCabang->password_ac = Hash::make($data['password']);
        }

        $adminCabang->nama_ac = $data['nama'];
        $adminCabang->telp_ac = $data['telp'];

        $adminCabang->save();

        return new AdminCabangResource($adminCabang);
    }

    public function delete(int $cabangId, int $adminCabangId)
    {
        $cabang = Cabang::find($cabangId);
        if (!$cabang) {
            return response()->json(["message" => "Cabang Tidak Ditemukan"], 404);
        }
        $adminCabang = $cabang->adminCabangs()->find($adminCabangId);
        if (!$adminCabang) {
            return response()->json(["message" => "Admin Cabang Tidak Ditemukan"], 404);
        }
        DB::transaction(function () use ($cabang, $adminCabang) {
            $adminCabang->delete();
        });

        return response()->json([
            "message" => "success delete admin cabang"
        ]);
    }

    public function getByid(int $cabangId, $adminCabangId)
    {
        $cabang = Cabang::find($cabangId);
        if (!$cabang) {
            return response()->json(["message" => "Cabang Tidak Ditemukan"], 404);
        }
        $adminCabang = $cabang->adminCabangs()->find($adminCabangId);
        if (!$adminCabang) {
            return response()->json(["message" => "Admin Cabang Tidak Ditemukan"], 404);
        }
        return new AdminCabangResource($adminCabang);
    }

    public function login(AdminCabangLoginRequest $request)
    {
        $data = $request->validated();
        $adminCabang = AdminCabang::where("email_ac", $data['email'])->first();
        if (!$adminCabang || !Hash::check($data['password'], $adminCabang->password_ac)) {
            return response()->json(["message" => "Password atau Email, Salah !"], 401);
        }
        $token = $adminCabang->createToken("auth_token")->plainTextToken;
        return response()->json([
            "data" => [
                "nama" => $adminCabang->nama_ac,
                "email" => $adminCabang->email_ac,
                "token" => $token,
            ]
        ]);
    }
    public function profile()
    {
        $adminCabang = auth("admin_cabang")->user();
        return new AdminCabangResource($adminCabang);
    }
    public function logout()
    {
        $adminCabang = auth("admin_cabang")->user();
        $adminCabang->currentAccessToken()->delete();
        return response()->json([
            "message" => "Logout berhasil"
        ]);
    }
}
