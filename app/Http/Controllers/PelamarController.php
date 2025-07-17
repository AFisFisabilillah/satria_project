<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\PelamarRegisterRequest;
use App\Http\Resources\PelamarResource;
use App\Models\Pelamar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class PelamarController extends Controller
{
    public function login(LoginRequest $request){
        $data = $request->validated();

        $pelamar = Pelamar::where('email_pelamar',$data['email'])->first();

        if(!$pelamar || !Hash::check($data['password'],$pelamar->password_pelamar)){
            return response()->json([
                "status"=>"error",
                "message"=>"email or password is wrong"
            ],403);
        }

        $token = $pelamar->createToken('auth_token')->plainTextToken;

        return response()->json([
            "data"=>[
                "nama"=>$pelamar->nama_pelamar,
                "email"=>$pelamar->email_pelamar,
                "token"=>$token,
            ]
        ]);

    }

    public function register(PelamarRegisterRequest $request){
        $data = $request->validated();
        $pelamar =  Pelamar::create([
            "nama_pelamar" => $data['nama'],
            "email_pelamar" => $data['email'],
            "telp_pelamar" => $data['telp'],
            "domisili_pelamar" => $data['domisili'],
            "password_pelamar" => Hash::make($data['password']),
        ]);




        return response()->json([
                "data"=>[
                    "name"=>$pelamar->nama_pelamar,
                    "email"=>$pelamar->email_pelamar,
                    "telp"=>$pelamar->telp_pelamar,
                    "domisili"=>$pelamar->domisili_pelamar,
                ]
            ],201);

    }

    public function logout(Request $request){

        auth("pelamar")->user()->currentAccessToken()->delete();
        return response()->json([
            "status"=>"success",
            "message"=>"Success Logged out"
        ]);
    }

    public function profile()
    {
        $pelamar = auth()->user();

        return new PelamarResource($pelamar);
    }
}
