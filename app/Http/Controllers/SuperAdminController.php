<?php

namespace App\Http\Controllers;

use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginSuperAdminRequest;

class SuperAdminController extends Controller
{
    public function login(LoginSuperAdminRequest $request){
        $data = $request->validated();

        $super_admin = SuperAdmin::where('email_super_admin', $data['email'])->first();

        if(!$super_admin || !Hash::check($data['password'], $super_admin->password_super_admin)){
            return response()->json([
                'status' => 'error',
                'message' => 'Email or password wrong'
            ], 403);
        }

        $token = $super_admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            "status" => "success",
            "data" => [
                "name" => $super_admin->name_super_admin,
                "email" => $super_admin->email_super_admin,
                "token" => $token,
            ],
        ]);
    }

    public function logout(Request $request){
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            "message" => "Logged out successfully"
        ]);
    }

    public function profile(){
        $superAdmin = auth('super_admin')->user();

        return response()->json([
            "data" => [
                "nama" => $superAdmin->name_super_admin,
                "email" => $superAdmin->email_super_admin
            ],
        ]);
    }

    public function dashboard(){
       $data = DB::table('pelamars')->select(DB::raw('kelamin_pelamar as name'), DB::raw('count(*) as value'))->groupBy('name')->get();
       $countLowongan = DB::table('lowongans')->count();
       $countPendaftar = DB::table('pendaftarans')->count();
       $countPelamar = DB::table('pelamars')->count();
        $domisili = DB::table('pelamars')->select(DB::raw('domisili_pelamar as name'), DB::raw('count(*) as jumlah'))->groupBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                "gender" => $data,
                "total_lowongan" => $countLowongan,
                "total_kandidat" => $countPelamar,
                "total_pendaftar" => $countPendaftar,
                "domisili" => $domisili
            ]
            ]);
    }
}
