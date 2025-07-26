<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminDirekturRequest;
use App\Http\Requests\AdminDirekturUpdateRequest;
use App\Http\Resources\AdminDirekturResource;
use App\Models\AdminDirektur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDirekturController extends Controller
{
    public function create(AdminDirekturRequest $request)
    {
        $data = $request->validated();
        $adminDirektur = AdminDirektur::create([
            "nama_direktur" => $data["nama"],
            "email_direktur" => $data["email"],
            "telp_direktur" => $data["telp"],
            "jabatan_direktur" => $data["jabatan"],
            "password_direktur" => Hash::make($data["password"])
        ]);

        return new AdminDirekturResource($adminDirektur);
    }

    public function getAll(Request $request)
    {

        $adminDirektur = AdminDirektur::paginate($request->get("size",10));

        return AdminDirekturResource::collection($adminDirektur);
    }

    public function updateAdmin(AdminDirekturUpdateRequest $request, int $adminDirekturId )
    {
        $data = $request->validated();

        $adminDirektur = AdminDirektur::find($adminDirekturId);
        if (!$adminDirektur) {
            return response()->json(["message" => "Direktur tidak ditemukan"], 404);
        }
        $adminDirektur->nama_direktur = $data["nama"];
        $adminDirektur->jabatan_direktur = $data["jabatan"];
        $adminDirektur->telp_direktur = $data["telp"];
        if($data["password"]){
            $adminDirektur->password_direktur = Hash::make($data["password"]);
        }
        $adminDirektur->save();

        return new AdminDirekturResource($adminDirektur);
    }

    public function delete(int $adminDirekturId){
        $adminDirektur = AdminDirektur::find($adminDirekturId);
        if (!$adminDirektur) {
            return response()->json(["message" => "Direktur tidak ditemukan"], 404);
        }
        $adminDirektur->delete();
        return response()->json(["message" => "Direktur telah dihapus"], 200);
    }

    public function get(int $adminDirekturId){
        $adminDirektur = AdminDirektur::find($adminDirekturId);
        if (!$adminDirektur) {
            return response()->json(["message" => "Direktur tidak ditemukan"], 404);
        }
        return new AdminDirekturResource($adminDirektur);
    }

    public function login(Request $request){
        $data = $request->validate([
            "email"=>"required|email|string",
            "password"=>"required|string"
        ]);

        $adminDirektur = AdminDirektur::where("email_direktur",$data["email"])->first();
        if (!$adminDirektur || !Hash::check($data["password"], $adminDirektur->password_direktur)) {
            return response()->json(["message" => "email atau password gagal"], 404);
        }
        $token = $adminDirektur->createToken("auth_token")->plainTextToken;
        return response()->json([
            "data"=>[
                "email"=>$adminDirektur->email_direktur,
                "nama"=>$adminDirektur->nama_direktur,
                "token"=>$token
            ]
        ]);
    }

    public function profile()
    {
        $adminDirektur  = auth("admin_direktur")->user();
        return new AdminDirekturResource($adminDirektur);
    }


    public function logout(){
        auth("admin_direktur")->logout();
    }
}
