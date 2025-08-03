<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PelamarController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminCabangController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\AdminDirekturController;

Route::post("/pelamar/register", [PelamarController::class, "register"]);
Route::post("/pelamar/login", [PelamarController::class, "login"]);
Route::get("/lowongan/search",[LowonganController::class, "searchLowongan"]);
Route::get("/lowongan/{lowonganId}",[LowonganController::class, "getLowonganById"]);

Route::get("/filter/domisili", [PelamarController::class, "getAllDomisili"]);
Route::get("/filter/negara", [LowonganController::class, "filterNegara"]);
Route::get("/filter/posisi", [LowonganController::class, "filterPosisi"]);

Route::middleware(['auth:pelamar'])->group(function () {
   Route::get("/pelamar/profile", [PelamarController::class, "profile"]);
   Route::get("/pelamar/logout", [PelamarController::class, "logout"]);
   Route::post("/pelamar/update", [PelamarController::class, "profileComplete"]);

   Route::get("/pelamar/lowongan/{lowonganId}/lamar", [PelamarController::class, "lamar"]);
   Route::get("/pelamar/lowongan/{lowonganId}/pendaftaran", [\App\Http\Controllers\PendaftaranController::class, "getDetailPendaftaranByLowonganId"]);

   Route::get("/pelamar/pendaftaran", [\App\Http\Controllers\PendaftaranController::class, "userPendaftaran"]);
   Route::get("/pelamar/pendaftaran/{pendaftranId}", [\App\Http\Controllers\PendaftaranController::class, "userGetPendaftaranById"]);
});

//Super ADMIN
Route::post("/super-admin/login", [SuperAdminController::class, "login"]);
Route::middleware(['auth:super_admin'])->group(function () {
    Route::get("/super-admin/dashboard", [SuperAdminController::class, "dashboard"]);
    Route::get("/super-admin/profile", [SuperAdminController::class, "profile"]);
    Route::get("/super-admin/logout", [SuperAdminController::class, "logout"]);
    Route::post("/super-admin/update-profile", [SuperAdminController::class, "update_profile"]);

//    Super admin

//    Cabang
    Route::post("/cabang", [CabangController::class, "create"]);
    Route::get("/cabang", [CabangController::class, "getCabang"]);
    Route::get("/cabang/{cabangId}", [CabangController::class, "getCabangById"]);
    Route::put("/cabang/{cabangId}", [CabangController::class, "update"]);
    Route::delete("/cabang/{cabangId}", [CabangController::class, "delete"]);

//    Admin Cabang
    Route::post("/cabang/{cabangId}/admin-cabang", [AdminCabangController::class, "create"]);
    Route::get("/cabang/{cabangId}/admin-cabang", [AdminCabangController::class, "getAllAdminCabangs"]);
    Route::post("/cabang/{cabangId}/admin-cabang/{adminCabangId}", [AdminCabangController::class, "update"]);
    Route::delete("/cabang/{cabangId}/admin-cabang/{adminCabangId}",[AdminCabangController::class, "delete"]);
    Route::get("/cabang/{cabangId}/admin-cabang/{adminCabangId}",[AdminCabangController::class, "getByid"]);

//    Lowongan
    Route::post("/super-admin/lowongan",[LowonganController::class, "create"]);
    Route::get("/super-admin/lowongan/search",[LowonganController::class, "searchLowongan"]);
    Route::get("/super-admin/lowongan/{lowonganId}", [LowonganController::class, "getLowonganById"]);
    Route::put("/super-admin/lowongan/{lowonganId}", [LowonganController::class, "update"]);
    Route::delete("/super-admin/lowongan/{lowonganId}", [LowonganController::class, "delete"]);

    Route::get("/super-admin/pelamar",[PelamarController::class, "superAdminGetAllPelamar"]);
    Route::get("/super-admin/pelamar/{pelamarId}",[PelamarController::class, "superAdminGetDetailPelamar"]);
    Route::patch("/super-admin/pelamar/{pelamarId}",[PelamarController::class, "changePassword"]);
    Route::get("/super-admin/pelamar/{pelamarId}/pendaftaran",[PelamarController::class, "getAllPendaftaranByUser"]);
    Route::patch("/super-admin/pelamar/{pelamarId}/reject",[PendaftaranController::class, "changeStatusToReject"]);

//    Admin direktur
    Route::post("/super-admin/admin-direktur",[AdminDirekturController::class, "create"]);
    Route::get("/super-admin/admin-direktur",[AdminDirekturController::class, "getAll"]);
    Route::get("/super-admin/admin-direktur/{adminDirekturId}",[AdminDirekturController::class, "get"]);
    Route::post("/super-admin/admin/{adminDirekturId}",[AdminDirekturController::class, "updateAdmin"]);
    Route::delete("/super-admin/admin-direktur/{adminDirekturId}",[AdminDirekturController::class, "delete"]);
//    Lowongan
    Route::get("/super-admin/pendaftaran", [PendaftaranController::class, "getAll"]);
    Route::get("/super-admin/lowongan/{lowonganId}/pendaftaran",[PendaftaranController::class, "getAllPendaftaranByLowonganId"]);
    Route::patch("/super-admin/lowongan/{lowonganId}/pendaftaran/{pendaftaranId}/review-by-hr",[PendaftaranController::class, "changeStatusToRiviewedByHrd"]);

     Route::post("/super-admin", [SuperAdminController::class, "create"]);
    Route::post("/super-admin/{superAdminId}", [SuperAdminController::class, "update"]);
    Route::delete("/super-admin/{superAdminId}", [SuperAdminController::class, "delete"]);
    Route::get("/super-admin", [SuperAdminController::class, "getAll"]);
    Route::get("/super-admin/{superAdminId}", [SuperAdminController::class, "getById"]);

});

//Admin Cabang
Route::post("/admin-cabang/login", [AdminCabangController::class, "login"]);
Route::middleware(['auth:admin_cabang'])->group(function () {
    Route::get("/admin-cabang/dashboard", [SuperAdminController::class, "dashboard"]);
    Route::get("/admin-cabang/profile", [AdminCabangController::class, "profile"]);
    Route::get("/admin-cabang/logout", [AdminCabangController::class, "logout"]);
    Route::post("/admin-cabang/update-profile", [AdminCabangController::class, "update_profile"]);

//    Pendaftaran
    Route::get("/admin-cabang/pendaftaran/{pendaftaranId}/follow-up",[PendaftaranController::class, "followup" ] );
    Route::get("/admin-cabang/lowongan/{lowonganId}/pendaftaran",[\App\Http\Controllers\PendaftaranController::class, "getAllPendaftaranByLowonganId"]);
    Route::patch("/admin-cabang/pendaftaran/{pendaftaranId}/review-by-hr",[PendaftaranController::class, "changeStatusToRiviewedByHrd"]);
    Route::patch("/admin-cabang/pendaftaran/{pendaftaranId}/interview",[PendaftaranController::class, "changeStatusToInterview"]);
    Route::patch("/admin-cabang/pendaftaran/{pendaftaranId}/accepted",[PendaftaranController::class, "changeStatusToAccepted"]);
    Route::patch("/admin-cabang/pendaftaran/{pendaftaranId}/rejected",[PendaftaranController::class, "changeStatusToReject"]);
    Route::get("/admin-cabang/pendaftaran/cabang",[PendaftaranController::class, "getByCabang"]);
    Route::get("/admin-cabang/pendaftaran", [PendaftaranController::class, "getAllPendaftaran"]);
});

Route::middleware("auth:admin_direktur")->group(function () {
    Route::get("/admin-direktur/profile", [AdminDirekturController::class, "profile"]);
});

Route::post("/admin-direktur/login", [AdminDirekturController::class, "login"]);


Route::middleware('auth:sanctum')->group(function () {
    // Rute untuk Pelamar
    Route::post('/pelamar/send-to-admin-cabang', [MessageController::class, 'sendToAdminCabang']);
    Route::post('/pelamar/send-to-super-admin', [MessageController::class, 'sendToSuperAdmin']);
    
    // Rute untuk Admin Cabang
    Route::post('/admin-cabang/send-to-pelamar', [MessageController::class, 'sendToPelamarFromAdminCabang']);
    
    // Rute untuk Super Admin (bisa mengirim ke semua)
    Route::post('/super-admin/send-to-pelamar', [MessageController::class, 'sendToPelamarFromSuperAdmin']);
    
    // Rute untuk mengambil pesan (tetap sama)
    Route::get('/messages', [MessageController::class, 'index']);
});