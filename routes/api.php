<?php

use App\Http\Controllers\AdminCabangController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\PelamarController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/pelamar/register", [PelamarController::class, "register"]);
Route::post("/pelamar/login", [PelamarController::class, "login"]);

Route::middleware(['auth:pelamar'])->group(function () {
   Route::get("/pelamar/profile", [PelamarController::class, "profile"]);
   Route::get("/pelamar/logout", [PelamarController::class, "logout"]);
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
