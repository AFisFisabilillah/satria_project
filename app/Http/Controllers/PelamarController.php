<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePassword;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PelamarRegisterRequest;
use App\Http\Requests\ProfileCompleteRequest;
use App\Http\Resources\PelamarResource;
use App\Http\Resources\PelamarSimpleResource;
use App\Http\Resources\PendaftaranResource;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Pendaftaran;
use App\Models\StatusHistory;
use App\StatusPendaftaran;
use Carbon\Carbon;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PelamarController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $pelamar = Pelamar::where('email_pelamar', $data['email'])->first();

        if (!$pelamar || !Hash::check($data['password'], $pelamar->password_pelamar)) {
            return response()->json([
                "status" => "error",
                "message" => "email or password is wrong"
            ], 403);
        }

        $token = $pelamar->createToken('auth_token')->plainTextToken;

        return response()->json([
            "data" => [
                "nama" => $pelamar->nama_pelamar,
                "email" => $pelamar->email_pelamar,
                "token" => $token,
            ]
        ]);

    }

    public function register(PelamarRegisterRequest $request)
    {
        $data = $request->validated();
        $pelamar = Pelamar::create([
            "nama_pelamar" => $data['nama'],
            "email_pelamar" => $data['email'],
            "telp_pelamar" => $data['telp'],
            "domisili_pelamar" => $data['domisili'],
            "password_pelamar" => Hash::make($data['password']),
        ]);


        return response()->json([
            "data" => [
                "name" => $pelamar->nama_pelamar,
                "email" => $pelamar->email_pelamar,
                "telp" => $pelamar->telp_pelamar,
                "domisili" => $pelamar->domisili_pelamar,
            ]
        ], 201);

    }

    public function logout(Request $request)
    {

        auth("pelamar")->user()->currentAccessToken()->delete();
        return response()->json([
            "status" => "success",
            "message" => "Success Logged out"
        ]);
    }

    public function profile()
    {
        $pelamar = auth()->user();

        return new PelamarResource($pelamar);
    }

    private function handleFileUpload($file, $folder, $oldPathFile, $fileNamePrefix, $nama = "")
    {
        if (!empty($oldPathFile)&&Storage::disk("public")->exists($oldPathFile)) {
            Storage::disk("public")->delete($oldPathFile);
        }
        $newFileName = "$fileNamePrefix" . "_" . Str::uuid() . "_" . Str::replace(" ", "_", $nama) . "." . $file->getClientOriginalExtension();
        $file->storeAs($folder, $newFileName, "public");
        return $folder . "/" . $newFileName;
    }

    public function profileComplete(ProfileCompleteRequest $request)
    {
        $data = $request->validated();

        $pelamar = auth("pelamar")->user();


        $cv = $request->file("cv", null);
        if ($cv) {
            $filename = $this->handleFileUpload($cv, "cv", $pelamar->cv_pelamar, "cv", $pelamar->nama_pelamar);
            $pelamar->cv_pelamar = $filename;
        } else if ($pelamar->cv_pelamar === null) {
            return response()->json([
                "message" => "cv harus di isi",
            ], 422);
        }


        $profile = $request->file("profile");
        if ($profile) {
            $filename = $this->handleFileUpload($profile, "profile", $pelamar->profile_pelamar, "profile", $pelamar->nama_pelamar);
            $pelamar->profile_pelamar = $filename;
        } else if (!$pelamar->profile_pelamar) {
            return response()->json([
                "message" => "profile harus di isi",
            ], 422);
        }
        $pelamar->kelamin_pelamar = $data["jenis_kelamin"];
        $pelamar->status_nikah_pelamar = $data["status_nikah"];
        $pelamar->ttl_pelamar = $data["tanggal_lahir"];
        $pelamar->sudah_lengkap = true;
        $pelamar->save();

        return new PelamarResource($pelamar);
    }

    public function lamar(int $lowonganId)
    {
        $lowongan = Lowongan::find($lowonganId);
        $pelamar = auth("pelamar")->user();

        $sudahDaftar = Pendaftaran::where('lowongan_id', $lowonganId)
            ->where('pelamar_id', $pelamar->id_pelamar)
            ->exists();

        if(!$lowongan){
            return response()->json(["message" => "Lowongan tidak ditemukan"], 404);
        }

        if ($sudahDaftar) {
            return response()->json([
                "message" => "Kamu sudah mendaftar lowongan ini"
            ], 400);
        }

        if ($lowongan->kuota_lowongan== 0) {
            return response()->json([
                "message" => "maaf kuota lowongan telah habis",
            ], 400);
        }


        if (!$pelamar->sudah_lengkap) {
            return response()->json([
                "message" => "lengkain data profile terlebih dahulu"
            ], 400);
        }


        try{
            DB::beginTransaction();
            $pendaftaran = Pendaftaran::create([
                "lowongan_id" => $lowonganId,
                "pelamar_id" => $pelamar->id_pelamar,
                "waktu_pendaftaran" => Carbon::now(),
                "status_pendaftaran" => StatusPendaftaran::Submitted->value,
            ]);

            $statusHistory = StatusHistory::create([
                "pendaftaran_id" => $pendaftaran->id_pendaftaran,
                "status" => StatusPendaftaran::Submitted->value,
            ]);

            $lowongan->update([
                "kuota_lowongan" => $lowongan->kuota_lowongan - 1
            ]);

            DB::commit();

//        Memasukan secara manual
            $pendaftaran->lowongan = $lowongan;
            $pendaftaran->statusHistories = [$statusHistory];

            return new PendaftaranResource($pendaftaran);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(["message"=>$e->getMessage()],500);
        }


    }

    public function superAdminGetAllPelamar()
    {
        $size = Request::get("size", 10);
        $q = Request::get("q", null);
        $email = Request::get("email", null);
        $domisili = Request::get("domsili", null);


        $pelamar = Pelamar::when($q, function ($query) use ($q) {
            $query->whereLike("nama_pelamar", "%$q%");
        })->when($email, function ($query) use ($email) {
            $query->whereLike("email_pelamar", "%$email%");
        })->when($domisili, function ($query) use ($domisili) {
            $query->where("domisili_pelamar", "$domisili");
        })->paginate($size);
        return PelamarSimpleResource::collection($pelamar);
    }

    public function superAdminGetDetailPelamar(int $pelamarId)
    {
        $pelamar = Pelamar::find($pelamarId);

        return new PelamarResource($pelamar);
    }

    public function changePassword(int $pelamarId,ChangePasswordRequest $request){
        $data = $request->validated();
        $pelamar = Pelamar::find($pelamarId);
        if (!$pelamar) {
            return response()->json(["message"=>"pelamar Not Found!"], 404);
        }

        $pelamar->update([
            "password_pelamar" => Hash::make($data["new_password"])
        ]);

        return new PelamarResource($pelamar);
    }

}
