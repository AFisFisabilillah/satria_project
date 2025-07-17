<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginSuperAdminRequest;
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
}
