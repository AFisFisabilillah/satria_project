<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatDetail;
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

        // Dapatkan ID pengguna yang sedang login secara dinamis
        $userId = null;
        if ($user instanceof Pelamar) {
            $userId = $user->id_pelamar;
        } elseif ($user instanceof AdminCabang || $user instanceof SuperAdmin) {
            $userId = $user->id;
        }

        if (!$userId) {
            // Handle case where user type is not recognized
            return response()->json(['messages' => []]);
        }

        $messages = Message::where(function ($query) use ($user, $userId) {
            $query->where('sender_id', $userId)
                ->where('sender_type', get_class($user));
        })->orWhere(function ($query) use ($user, $userId) {
            $query->where('receiver_id', $userId)
                ->where('receiver_type', get_class($user));
        })
            ->with('sender', 'receiver')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['messages' => $messages]);
    }

    public function getChatPelamarsForAdminCabang()
    {
        $adminCabang = Auth::user();

        // Pastikan pengguna adalah AdminCabang
        if (!$adminCabang instanceof AdminCabang) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Ambil semua ID Pelamar yang pernah mengirim pesan ke AdminCabang ini
        $pelamarIds = Message::where('receiver_id', $adminCabang->id)
            ->where('receiver_type', get_class($adminCabang))
            ->pluck('sender_id') // Ambil kolom sender_id
            ->unique(); // Pastikan ID unik

        // Ambil data Pelamar berdasarkan ID yang sudah didapat
        $pelamars = Pelamar::whereIn('id_pelamar', $pelamarIds)->get();

        return response()->json(['data' => $pelamars]);
    }

    public function getChatPelamarsForSuperAdmin()
    {
        $superAdmin = Auth::user();

        // Pastikan pengguna adalah SuperAdmin
        if (!$superAdmin instanceof SuperAdmin) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Ambil semua ID Pelamar yang pernah mengirim pesan ke SuperAdmin ini
        $pelamarIds = Message::where('receiver_id', $superAdmin->id)
            ->where('receiver_type', get_class($superAdmin))
            ->pluck('sender_id')
            ->unique();

        $pelamars = Pelamar::whereIn('id_pelamar', $pelamarIds)->get();

        return response()->json(['data' => $pelamars]);
    }


     public function getConversationWithPelamar(Request $request, $pelamarId)
    {
        $user = Auth::user();

        // Tentukan ID pengguna yang login
        $userId = ($user instanceof Pelamar) ? $user->id_pelamar : $user->id;

        // Ambil nama kelas model Pelamar
        $pelamarType = Pelamar::class;

        // Validasi bahwa pengirim adalah AdminCabang atau SuperAdmin
        if (!($user instanceof AdminCabang || $user instanceof SuperAdmin)) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Query untuk mengambil pesan antara user yang login dan pelamarId
        $messages = Message::where(function ($query) use ($userId, $user, $pelamarId, $pelamarType) {
            $query->where('sender_id', $userId)
                  ->where('sender_type', get_class($user))
                  ->where('receiver_id', $pelamarId)
                  ->where('receiver_type', $pelamarType);
        })->orWhere(function ($query) use ($userId, $user, $pelamarId, $pelamarType) {
            $query->where('sender_id', $pelamarId)
                  ->where('sender_type', $pelamarType)
                  ->where('receiver_id', $userId)
                  ->where('receiver_type', get_class($user));
        })
        ->with('sender', 'receiver')
        ->orderBy('created_at', 'asc')
        ->get();

        return ChatDetail::collection($messages);
    }
}
