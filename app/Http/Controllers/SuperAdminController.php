<?php

namespace App\Http\Controllers;

use App\Models\SuperAdmin;
use App\Models\Pendaftaran;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SuperAdminResource;
use App\Http\Requests\LoginSuperAdminRequest;

class SuperAdminController extends Controller
{
    public function login(LoginSuperAdminRequest $request)
    {
        $data = $request->validated();

        $super_admin = SuperAdmin::where('email_super_admin', $data['email'])->first();

        if (!$super_admin || !Hash::check($data['password'], $super_admin->password_super_admin)) {
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

    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            "message" => "Logged out successfully"
        ]);
    }

    public function update_profile(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            "password" => "nullable|string|min:8",
            "profile" => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $superAdmin = auth('super_admin')->user();

        if (!$superAdmin) {
            return response()->json(["message" => "super_admin not found"], 404);
        }

        $profile = $request->file("profile");
        if ($profile) {
            // generate nama file unik
            $profileName = Str::uuid()->toString() . '.' . $profile->getClientOriginalExtension();
            $profilePath = $profile->storeAs('profiles', $profileName, 'public');
            $data['profile'] = $profilePath;

            // hapus file lama jika ada
            if ($superAdmin->photo_profile && Storage::disk('public')->exists($superAdmin->photo_profile)) {
                Storage::disk('public')->delete($superAdmin->photo_profile);
            }

            $superAdmin->photo_profile = $data['profile'];
        }

        if (!empty($data['password'])) {
            $superAdmin->password_super_admin = Hash::make($data['password']);
        }
        $superAdmin->name_super_admin = $data['nama'];
        $superAdmin->save();
        return new SuperAdminResource($superAdmin);
    }

    public function profile()
    {
        $superAdmin = auth('super_admin')->user();

        return response()->json([
            "data" => [
                "nama" => $superAdmin->name_super_admin,
                "email" => $superAdmin->email_super_admin,
                "profile" => asset("storage/" . $superAdmin->photo_profile)
            ],
        ]);
    }

    public function dashboard()
    {
        $data = DB::table('pelamars')
            ->whereNotNull('kelamin_pelamar')
            ->whereNull('deleted_at') // hanya pelamar yang belum dihapus
            ->select(DB::raw('kelamin_pelamar as name'), DB::raw('count(*) as value'))
            ->groupBy('name')
            ->get();

        $countLowongan = DB::table('lowongans')
            ->whereNull('deleted_at') // yang sudah dihapus
            ->count();

        $countPendaftar = DB::table('pendaftarans')
            ->whereNull('deleted_at') // hanya pendaftar yang belum dihapus
            ->count();

        $countPelamar = DB::table('pelamars')
            ->whereNull('deleted_at') // hanya pelamar yang belum dihapus
            ->count();

        $domisili = DB::table('pelamars')
            ->whereNotNull('domisili_pelamar')
            ->whereNull('deleted_at') // hanya pelamar yang belum dihapus
            ->select(DB::raw('domisili_pelamar as name'), DB::raw('count(*) as jumlah'))
            ->groupBy('name')
            ->get();

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



    public function create(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            "email" => 'required|string|email|max:255|unique:super_admins,email_super_admin',
            "password" => 'required|string|min:8',
            "profile" => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $profile = $request->file('profile');
        if ($profile) {
            $filename = Str::uuid()->toString() . '.' . $profile->getClientOriginalExtension();
            $path = $profile->storeAs('profiles', $filename, 'public');
            $data['photo_profile'] = $path;
        } else {
            $data['photo_profile'] = null;
        }

        $superAdmin = SuperAdmin::create([
            "name_super_admin" => $data['nama'],
            "email_super_admin" => $data['email'],
            "password_super_admin" => Hash::make($data['password']),
            "photo_profile" => $data['photo_profile'] ?? null,
        ]);

        return new SuperAdminResource($superAdmin);
    }

    public function update(Request $request, int  $superAdminId)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            "password" => "nullable|string|min:8",
            "profile" => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $superAdmin = SuperAdmin::find($superAdminId);

        if (!$superAdmin) {
            return response()->json(["message" => "super_admin not found"], 404);
        }

        $profile = $request->file("profile");
        if ($profile) {
            // generate nama file unik
            $profileName = Str::uuid()->toString() . '.' . $profile->getClientOriginalExtension();
            $profilePath = $profile->storeAs('profiles', $profileName, 'public');
            $data['profile'] = $profilePath;

            // hapus file lama jika ada
            if ($superAdmin->photo_profile && Storage::disk('public')->exists($superAdmin->photo_profile)) {
                Storage::disk('public')->delete($superAdmin->photo_profile);
            }

            $superAdmin->photo_profile = $data['profile'];
        }

        if (!empty($data['password'])) {
            $superAdmin->password_super_admin = Hash::make($data['password']);
        }
        $superAdmin->name_super_admin = $data['nama'];
        $superAdmin->save();
        return new SuperAdminResource($superAdmin);
    }

    public function delete(int  $superAdminId)
    {
        if ($superAdminId == 1) {
            return response()->json([
                "message" => "super admin default tidak boleh di hapus"
            ], 400);
        }
        $superAdmin = SuperAdmin::find($superAdminId);
        if (!$superAdmin) {
            return response()->json(["message" => "super_admin not found"], 404);
        }
        $superAdmin->delete();
        return response()->json(["message" => "super_admin deleted successfully"], 200);
    }

    public function getAll(Request $request)
    {
        $q = $request->get('q');
        $superAdmin = null;
        if ($q) {
            $superAdmin = SuperAdmin::where('name_super_admin', 'LIKE', '%' . $q . '%')
                ->orWhere('email_super_admin', 'LIKE', '%' . $q . '%')->paginate($request->get("size", 10));
        } else {
            $superAdmin = SuperAdmin::whereNot("id", 1)->paginate($request->get("size", 10));
        }


        return SuperAdminResource::collection($superAdmin);
    }

    public function getById($superAdminId)
    {
        $superAdmin = SuperAdmin::find($superAdminId);
        if (!$superAdmin) {
            return response()->json(["message" => "super_admin not found"], 404);
        }
        return new SuperAdminResource($superAdmin);
    }
}
