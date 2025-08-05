<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Pelamar;
use App\Models\SuperAdmin;
use App\Models\AdminCabang;
use Illuminate\Http\Request;
use App\Http\Resources\ChatDetail;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ChatListPelamarResource;

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
            'receiver_id' => 'required|exists:pelamars,id_pelamar',
            'message' => 'required|string',
        ]);

        $admin = auth("super_admin")->user();

        if (!$admin instanceof SuperAdmin) {
            return response()->json(['message' => 'Pengirim tidak diizinkan.'], 403);
        }

        $receiver = Pelamar::find($validatedData['receiver_id']);

        $message = $admin->sentMessages()->create([
            'receiver_id' => $receiver->id_pelamar,
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

        if (!$adminCabang instanceof AdminCabang) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Ambil semua pesan yang melibatkan AdminCabang dan Pelamar
        $messages = Message::where(function ($query) use ($adminCabang) {
            $query->where('sender_id', $adminCabang->id)
                ->where('sender_type', get_class($adminCabang));
        })
            ->orWhere(function ($query) use ($adminCabang) {
                $query->where('receiver_id', $adminCabang->id)
                    ->where('receiver_type', get_class($adminCabang));
            })
            ->with('sender') // agar tidak N+1
            ->orderByDesc('created_at')
            ->get();

        // Kelompokkan berdasarkan pasangan percakapan (Pelamar sebagai lawan bicara)
        $grouped = $messages->map(function ($msg) use ($adminCabang) {
            if ($msg->sender_id != $adminCabang->id || $msg->sender_type != get_class($adminCabang)) {
                // Admin sebagai penerima, Pelamar sebagai pengirim
                return [
                    'id' => $msg->sender_id,
                    'type' => $msg->sender_type,
                    'message' => $msg,
                ];
            } else {
                // Admin sebagai pengirim, Pelamar sebagai penerima
                return [
                    'id' => $msg->receiver_id,
                    'type' => $msg->receiver_type,
                    'message' => $msg,
                ];
            }
        })->filter(fn($item) => $item['type'] === Pelamar::class)
            ->unique(fn($item) => $item['type'] . '_' . $item['id'])
            ->values();

        // Ambil resource dan format response
        $final = $grouped->map(function ($item) {
            return new ChatListPelamarResource($item['message']);
        });

        return response()->json($final);
    }

    public function getChatPelamarsForSuperAdmin()
    {
        $adminCabang = Auth::user();

        if (!$adminCabang instanceof SuperAdmin) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Ambil semua pesan yang melibatkan AdminCabang dan Pelamar
        $messages = Message::where(function ($query) use ($adminCabang) {
            $query->where('sender_id', $adminCabang->id)
                ->where('sender_type', get_class($adminCabang));
        })
            ->orWhere(function ($query) use ($adminCabang) {
                $query->where('receiver_id', $adminCabang->id)
                    ->where('receiver_type', get_class($adminCabang));
            })
            ->with('sender') // agar tidak N+1
            ->orderByDesc('created_at')
            ->get();

        // Kelompokkan berdasarkan pasangan percakapan (Pelamar sebagai lawan bicara)
        $grouped = $messages->map(function ($msg) use ($adminCabang) {
            if ($msg->sender_id != $adminCabang->id || $msg->sender_type != get_class($adminCabang)) {
                // Admin sebagai penerima, Pelamar sebagai pengirim
                return [
                    'id' => $msg->sender_id,
                    'type' => $msg->sender_type,
                    'message' => $msg,
                ];
            } else {
                // Admin sebagai pengirim, Pelamar sebagai penerima
                return [
                    'id' => $msg->receiver_id,
                    'type' => $msg->receiver_type,
                    'message' => $msg,
                ];
            }
        })->filter(fn($item) => $item['type'] === Pelamar::class)
            ->unique(fn($item) => $item['type'] . '_' . $item['id'])
            ->values();

        // Ambil resource dan format response
        $final = $grouped->map(function ($item) {
            return new ChatListPelamarResource($item['message']);
        });

        return response()->json($final);
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



    public function getChatListForPelamar()
    {
        $pelamar = Auth::user();

        if (!$pelamar instanceof Pelamar) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Ambil semua pesan yang terkait dengan pelamar
        $messages = Message::where(function ($query) use ($pelamar) {
            $query->where('sender_id', $pelamar->id_pelamar)
                ->where('sender_type', get_class($pelamar));
        })
            ->orWhere(function ($query) use ($pelamar) {
                $query->where('receiver_id', $pelamar->id_pelamar)
                    ->where('receiver_type', get_class($pelamar));
            })
            ->latest('created_at') // ambil dari yang terbaru
            ->get();

        // Kelompokkan percakapan berdasarkan lawan bicara
        $grouped = $messages->map(function ($msg) use ($pelamar) {
            if ($msg->sender_id == $pelamar->id_pelamar && $msg->sender_type == get_class($pelamar)) {
                return [
                    'id' => $msg->receiver_id,
                    'type' => $msg->receiver_type,
                    'last_message' => $msg,
                ];
            } else {
                return [
                    'id' => $msg->sender_id,
                    'type' => $msg->sender_type,
                    'last_message' => $msg,
                ];
            }
        })->unique(fn($item) => $item['type'] . '_' . $item['id']);

        // Ambil data partner dan kembalikan sebagai array
        $partners = $grouped->map(function ($item) {
            $id = $item['id'];
            $type = $item['type'];
            $message = $item['last_message'];

            if ($type === AdminCabang::class) {
                $user = AdminCabang::find($id);
                return [
                    'id' => $user->id ?? null,
                    'type' => $type,
                    'nama' => $user->nama_ac ?? null,
                    'email' => $user->email_ac ?? null,
                    'terakhir_chat' => $message->message,
                    'waktu' => $message->created_at,
                ];
            } elseif ($type === SuperAdmin::class) {
                $user = SuperAdmin::find($id);
                return [
                    'id' => $user->id ?? null,
                    'type' => $type,
                    'nama' => $user->name_super_admin ?? null,
                    'email' => $user->email_super_admin ?? null,
                    'terakhir_chat' => $message->message,
                    'waktu' => $message->created_at,
                ];
            }

            return null;
        })->filter()->sortByDesc('waktu')->values();

        return response()->json($partners);
    }

    public function getConversationWithPartner(Request $request, $partnerId)
    {
        $user = Auth::user();

        // Validasi bahwa pengguna yang login adalah Pelamar
        if (!$user instanceof Pelamar) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Ambil tipe partner dari query parameter, misalnya: ?partner_type=App\Models\AdminCabang
        $partnerType = $request->query('partner_type');

        // Pastikan partnerType valid
        if (!in_array($partnerType, [AdminCabang::class, SuperAdmin::class])) {
            return response()->json(['message' => 'Tipe partner tidak valid.'], 400);
        }

        $userId = $user->id_pelamar;
        $userType = get_class($user);

        // Query untuk mengambil pesan antara Pelamar yang login dan partner yang dipilih
        $messages = Message::where(function ($query) use ($userId, $userType, $partnerId, $partnerType) {
            $query->where('sender_id', $userId)
                ->where('sender_type', $userType)
                ->where('receiver_id', $partnerId)
                ->where('receiver_type', $partnerType);
        })->orWhere(function ($query) use ($userId, $userType, $partnerId, $partnerType) {
            $query->where('sender_id', $partnerId)
                ->where('sender_type', $partnerType)
                ->where('receiver_id', $userId)
                ->where('receiver_type', $userType);
        })
            ->with('sender', 'receiver')
            ->orderBy('created_at', 'asc')
            ->get();

        return ChatDetail::collection($messages);
    }

    public function getConversationForAdmin(Request $request, $pelamarId)
    {
        $user = Auth::user();

        // Validasi bahwa pengguna yang login adalah AdminCabang atau SuperAdmin
        if (!($user instanceof AdminCabang || $user instanceof SuperAdmin)) {
            return response()->json(['message' => 'Akses ditolak. Anda bukan admin.'], 403);
        }

        // Tentukan ID dan tipe pengguna admin
        $adminId = $user->id;
        $adminType = get_class($user);

        // Tentukan ID dan tipe pengguna pelamar
        $pelamarType = Pelamar::class;

        // Query untuk mengambil pesan dua arah
        $messages = Message::where(function ($query) use ($adminId, $adminType, $pelamarId, $pelamarType) {
            // Kondisi 1: Admin mengirim ke Pelamar
            $query->where('sender_id', $adminId)
                ->where('sender_type', $adminType)
                ->where('receiver_id', $pelamarId)
                ->where('receiver_type', $pelamarType);
        })->orWhere(function ($query) use ($adminId, $adminType, $pelamarId, $pelamarType) {
            // Kondisi 2: Pelamar mengirim ke Admin
            $query->where('sender_id', $pelamarId)
                ->where('sender_type', $pelamarType)
                ->where('receiver_id', $adminId)
                ->where('receiver_type', $adminType);
        })
            ->with('sender', 'receiver')
            ->orderBy('created_at', 'asc')
            ->get();

        // Menggunakan resource ChatDetail yang sudah dimodifikasi
        return ChatDetail::collection($messages);
    }
}
