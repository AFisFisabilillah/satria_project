<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Pelamar;
use App\Models\AdminCabang;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // ... (metode index tetap sama)

    public function sendToAdminCabang(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|exists:admin_cabangs,id', // Validasi langsung ke tabel admin_cabangs
            'message' => 'required|string',
        ]);

        $sender = Auth::user();

        // Pastikan pengirim adalah Pelamar
        if (!$sender instanceof Pelamar) {
            return response()->json(['message' => 'Pengirim tidak diizinkan mengirim pesan.'], 403);
        }

        // Cari penerima
        $receiver = AdminCabang::find($validatedData['receiver_id']);
        if (!$receiver) {
            return response()->json(['message' => 'Penerima (Admin Cabang) tidak ditemukan.'], 404);
        }

        // Buat pesan
        $message = $sender->sentMessages()->create([
            'receiver_id' => $receiver->id,
            'receiver_type' => get_class($receiver),
            'message' => $validatedData['message'],
        ]);

        return response()->json(['message' => 'Pesan ke Admin Cabang terkirim.', 'data' => $message]);
    }

    public function sendToSuperAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|exists:super_admins,id', // Validasi langsung ke tabel super_admins
            'message' => 'required|string',
        ]);

        $sender = Auth::user();

        // Pastikan pengirim adalah Pelamar
        if (!$sender instanceof Pelamar) {
            return response()->json(['message' => 'Pengirim tidak diizinkan mengirim pesan.'], 403);
        }

        // Cari penerima
        $receiver = SuperAdmin::find($validatedData['receiver_id']);
        if (!$receiver) {
            return response()->json(['message' => 'Penerima (Super Admin) tidak ditemukan.'], 404);
        }

        // Buat pesan
        $message = $sender->sentMessages()->create([
            'receiver_id' => $receiver->id,
            'receiver_type' => get_class($receiver),
            'message' => $validatedData['message'],
        ]);

        return response()->json(['message' => 'Pesan ke Super Admin terkirim.', 'data' => $message]);
    }

     public function sendToPelamarFromAdminCabang(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|exists:pelamars,id_pelamar',
            'message' => 'required|string',
        ]);

        
        if (!Auth::user() instanceof AdminCabang) {
            return response()->json(['message' => 'Pengirim tidak diizinkan.'], 403);
        }

        $receiver = Pelamar::find($validatedData['receiver_id']);
        $message = Auth::user()->sentMessages()->create([
            'receiver_id' => $receiver->id_pelamar,
            'receiver_type' => get_class($receiver),
            'message' => $validatedData['message'],
        ]);

        return response()->json(['message' => 'Pesan terkirim.', 'data' => $message]);
    }
    
    // --- Metode untuk Pengiriman Pesan dari Super Admin ---
    public function sendToPelamarFromSuperAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|exists:pelamars,id',
            'message' => 'required|string',
        ]);

        if (!Auth::user() instanceof SuperAdmin) {
            return response()->json(['message' => 'Pengirim tidak diizinkan.'], 403);
        }
        
        $receiver = Pelamar::find($validatedData['receiver_id']);

        $message = Auth::user()->sentMessages()->create([
            'receiver_id' => $receiver->id,
            'receiver_type' => get_class($receiver),
            'message' => $validatedData['message'],
        ]);

        return response()->json(['message' => 'Pesan terkirim.', 'data' => $message]);
    }

    public function index()
    {
        $user = Auth::user();
        
        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('sender_type', get_class($user));
        })->orWhere(function ($query) use ($user) {
            $query->where('receiver_id', $user->id)
                  ->where('receiver_type', get_class($user));
        })->with('sender', 'receiver') // Eager load pengirim dan penerima
          ->orderBy('created_at', 'asc')
          ->get();
        
        return response()->json(['messages' => $messages]);
    }

}
