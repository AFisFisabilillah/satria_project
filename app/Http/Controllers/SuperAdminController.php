<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginSuperAdminRequest;
use App\Http\Resources\SuperAdminResource;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function create(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            "email" => 'required|string|email|max:255|unique:super_admins,email_super_admin',
            "password" => 'required|string|min:8',
        ]);

        $superAdmin = SuperAdmin::create([
            "name_super_admin" => $data['nama'],
            "email_super_admin" => $data['email'],
            "password_super_admin" => Hash::make($data['password'])
        ]);

        return new SuperAdminResource($superAdmin);
    }

    public function update(Request $request,int  $superAdminId){
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            "password"=>"nullable|string|min:8",
        ]);

        if($superAdminId == 1){
            return response()->json([
                "message"=>"super admin default tidak boleh di hapus"
            ],400);
        }

        $superAdmin = SuperAdmin::find($superAdminId);

        if(!$superAdmin){
            return response()->json(["message"=>"super_admin not found"],404);
        }

        if($data['password']){
            $superAdmin->password_super_admin = Hash::make($data['password']);
        }
        $superAdmin->name_super_admin = $data['nama'];
        $superAdmin->save();
        return new SuperAdminResource($superAdmin);
    }

    public function delete(int  $superAdminId){
        if($superAdminId == 1){
            return response()->json([
                "message"=>"super admin default tidak boleh di hapus"
            ],400);
        }
        $superAdmin = SuperAdmin::find($superAdminId);
        if(!$superAdmin){
            return response()->json(["message"=>"super_admin not found"],404);
        }
        $superAdmin->delete();
        return response()->json(["message"=>"super_admin deleted successfully"],200);
    }

    public function getAll(Request $request)
    {
        $q = $request->get('q');
        $superAdmin = null;
        if($q){
            $superAdmin = SuperAdmin::where('name_super_admin', 'LIKE', '%' . $q . '%')
                ->orWhere('email_super_admin', 'LIKE', '%' . $q . '%')->paginate($request->get("size", 10));
        }else{
            $superAdmin = SuperAdmin::whereNot("id", 1)->paginate($request->get("size", 10));
        }


        return SuperAdminResource::collection($superAdmin);
    }

}
