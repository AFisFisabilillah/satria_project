<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordCode;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use function Laravel\Prompts\password;

class ResetPasswordController extends Controller
{
    public function sendCode(Request $request){
        $data = $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = DB::table('pelamars')->where('email_pelamar', $data['email'])->first();
        if(!$user){
            return response()->json([
                "message" => "Email tidak ditemukan"
            ], 404);
        }

        $code = rand(100000, 999999);
        $expired_at = Carbon::now()->addMinutes(10);
        $reset_passord = DB::table('reset_passwords')->insert([
            "pelamar_id" => $user->id_pelamar,
            "verification_code" => $code,
            "expired_code" => $expired_at,
            "is_valid" => false,
            "is_used" => false,
        ]);

        if($reset_passord){
            Mail::to($user->email_pelamar)->send(
                new ResetPasswordCode($user->nama_pelamar, $code,$expired_at->format("d-m-Y H:i"))
            );
            return response()->json([
                "message" => "success mengirimkan code ke email anda"
            ]);
        }
    }

    public function verifyCode(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email',
            'code'  => 'required|string',
        ]);

        $user = DB::table('pelamars')
            ->select('id_pelamar')
            ->where('email_pelamar', $data['email'])
            ->first();

        if (!$user) {
            return response()->json(["message" => "Email tidak ditemukan"], 404);
        }

        $reset = DB::table('reset_passwords')
            ->where('pelamar_id', $user->id_pelamar)
            ->where('verification_code', $data['code'])
            ->where('is_used', false)
            ->latest('created_at')
            ->first();

        if (!$reset) {
            return response()->json(["message" => "Kode tidak valid atau sudah digunakan"], 400);
        }

        if($reset->is_valid == true){
            return response()->json([
                "message" => "Kode sudah digunakan"
            ],400);
        }

        if (Carbon::now()->isAfter($reset->expired_code)) {
            return response()->json(["message" => "Kode sudah kadaluarsa"], 400);
        }

        $token = Str::uuid()->toString();
        // update langsung sekali
        DB::table('reset_passwords')
            ->where('id', $reset->id)
            ->update(['is_valid' => true,
                "token" => $token,]);

        return response()->json([
            "message" => "Kode valid, silakan reset password",
            "token"   =>$token
        ]);
    }

    public function resetPassword(Request $request){
        $data = $request->validate([
            "password" => "required|string|min:8",
            "token" =>"required|string",
        ]);

        $reset = DB::table('reset_passwords')
            ->join("pelamars", "pelamars.id_pelamar", "=", "reset_passwords.pelamar_id")
            ->select("pelamars.id_pelamar","reset_passwords.id","reset_passwords.is_valid", "reset_passwords.is_used")
            ->where('token', $data['token'])->first();

        if(!$reset->id){
            return response()->json(["message" => "Token tidak ditemukan"], 404);
        }

        if($reset->is_valid != true){
            return response()->json(["message" => "Kode tidak valid"], 400);
        }

        if ($reset->is_used == true){
            return response()->json(["message" => "token sudah digunakan"], 400);
        }
        $isSuccess = DB::table("pelamars")
            ->where("id_pelamar", $reset->id_pelamar)
            ->update([
                "password_pelamar" => Hash::make($data['password']),
            ]);

        if (!$isSuccess) {
            return response()->json(["message" => "Gagal mengubah password"], 400);
        }

        return response()->json([
            "message" => "Berhasil mengubah password"
        ]);
    }
    public function resendCode(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = DB::table('pelamars')->where('email_pelamar', $data['email'])->select("id_pelamar", "email_pelamar", "nama_pelamar")->first();

        if (!$user) {
            return response()->json(["message" => "Email tidak ditemukan"], 404);
        }
        $reset = DB::table('reset_passwords')
            ->where("is_valid", false)
            ->where('pelamar_id', $user->id_pelamar)
            ->orderBy('created_at', 'desc')
            ->first();


        if(!$reset){
            $code =  rand(100000, 999999);
            $expired_at = Carbon::now()->addMinutes(10);
            $isSuccess = DB::table('reset_passwords')->insert([
                "pelamar_id" => $user->id_pelamar,
                "verification_code" => $code,
                "expired_code" => $expired_at,
                "is_valid" => false,
                "is_used" => false,
            ]);

            if ($isSuccess) {
                Mail::to($user->email_pelamar)->send(
                    new ResetPasswordCode($user->nama_pelamar, $code,$expired_at)
                );
                return response()->json([
                    "message" => "success mengirimkan code ke email anda"
                ], 200);
            }else{
                return response()->json(["message" => "gagal resend code"], 404);
            }
        }else{
            $code =  rand(100000, 999999);
            $expired_at = Carbon::now()->addMinutes(10);

            $isSuccess = DB::table('reset_passwords')->where("id", $reset->id)->update([
                "verification_code" => $code,
                "expired_code" => $expired_at,
            ]);

            if ($isSuccess) {
                Mail::to($user->email_pelamar)->send(
                    new ResetPasswordCode($user->nama_pelamar, $code,$expired_at)
                );
                return response()->json([
                    "message" => "success mengirimkan code ke email anda"
                ], 200);
            }else{
                return response()->json(["message" => "gagal resend code"], 404);
            }

        }

    }

}
