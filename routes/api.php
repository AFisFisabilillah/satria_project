<?php

use App\Http\Controllers\PelamarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/pelamar/register", [PelamarController::class, "register"]);
Route::post("/pelamar/login", [PelamarController::class, "login"]);
Route::post("/pelamar/logout", [PelamarController::class, "logout"]);
