<?php

use App\Http\Controllers\AdminCabangController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\PelamarController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/pelamar/register", [PelamarController::class, "register"]);
Route::post("/pelamar/login", [PelamarController::class, "login"]);
Route::get("/lowongan/search",[LowonganController::class, "searchLowongan"]);
Route::get("/filter/negara", [LowonganController::class, "filterNegara"]);
Route::get("/filter/negara", [LowonganController::class, "filterNegara"]);
Route::get("/filter/posisi", [LowonganController::class, "filterPosisi"]);

Route::middleware(['auth:pelamar'])->group(function () {
   Route::get("/pelamar/profile", [PelamarController::class, "profile"]);
   Route::get("/pelamar/logout", [PelamarController::class, "logout"]);
   Route::post("/pelamar/update", [PelamarController::class, "profileComplete"]);

   Route::get("/pelamar/lowongan/{lowonganId}/lamar", [PelamarController::class, "lamar"]);
   Route::get("/pelamar/lowongan/{lowonganId}", [LowonganController::class, "userGetLowonganById"]);
   Route::get("/pelamar/lowongan/{lowonganId}/pendaftaran", [\App\Http\Controllers\PendaftaranController::class, "getDetailPendaftaranByLowonganId"]);

   Route::get("/pelamar/pendaftaran", [\App\Http\Controllers\PendaftaranController::class, "userPendaftaran"]);
   Route::get("/pelamar/pendaftaran/{pendaftranId}", [\App\Http\Controllers\PendaftaranController::class, "userGetPendaftaranById"]);
});

//Super ADMIN
Route::post("/super-admin/login", [SuperAdminController::class, "login"]);
Route::middleware(['auth:super_admin'])->group(function () {
    Route::get("/super-admin/profile", [SuperAdminController::class, "profile"]);
    Route::get("/super-admin/logout", [SuperAdminController::class, "logout"]);

//    Cabang
    Route::post("/cabang", [CabangController::class, "create"]);
    Route::get("/cabang", [CabangController::class, "getCabang"]);
    Route::get("/cabang/{cabangId}", [CabangController::class, "getCabangById"]);
    Route::put("/cabang/{cabangId}", [CabangController::class, "update"]);
    Route::delete("/cabang/{cabangId}", [CabangController::class, "delete"]);

//    Admin Cabang
    Route::post("/cabang/{cabangId}/admin-cabang", [AdminCabangController::class, "create"]);
    Route::get("/cabang/{cabangId}/admin-cabang", [AdminCabangController::class, "getAllAdminCabangs"]);
    Route::put("/cabang/{cabangId}/admin-cabang/{adminCabangId}", [AdminCabangController::class, "update"]);
    Route::delete("/cabang/{cabangId}/admin-cabang/{adminCabangId}",[AdminCabangController::class, "delete"]);
    Route::get("/cabang/{cabangId}/admin-cabang/{adminCabangId}",[AdminCabangController::class, "getByid"]);
});

//Admin Cabang
Route::post("/admin-cabang/login", [AdminCabangController::class, "login"]);
Route::middleware(['auth:admin_cabang'])->group(function () {
    Route::get("/admin-cabang/profile", [AdminCabangController::class, "profile"]);
    Route::get("/admin-cabang/logout", [AdminCabangController::class, "logout"]);

//    Lowongan
    Route::post("/admin-cabang/lowongan", [LowonganController::class, "create"]);
    Route::get("/admin-cabang/lowongan/{lowonganId}", [LowonganController::class, "getLowonganById"]);
    Route::put("/admin-cabang/lowongan/{lowonganId}", [LowonganController::class, "update"]);
    Route::delete("/admin-cabang/lowongan/{lowonganId}", [LowonganController::class, "delete"]);
    Route::get("/admin-cabang/lowongan",[LowonganController::class, "searchLowonganAdminCabang"]);
});

//public
