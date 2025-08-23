<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArtikelRequest;
use App\Http\Requests\ArtikelUpdateRequest;
use App\Http\Resources\ArtikelResource;
use App\Models\Artikel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtikelController extends Controller
{
    private function handleFileUpload($file, $folder, $oldPathFile = "", $fileNamePrefix, $nama = "")
    {
        if (!empty($oldPathFile) && Storage::disk("public")->exists($oldPathFile)) {
            Storage::disk("public")->delete($oldPathFile);
        }
        $newFileName = "{$fileNamePrefix}_" . Str::uuid() . "_" . Str::replace(" ", "_", $nama) . "." . $file->getClientOriginalExtension();
        $file->storeAs($folder, $newFileName, "public");
        return "{$folder}/{$newFileName}";
    }

    public function create(ArtikelRequest $request)
    {
        $admin = auth()->user();
        $data = $request->validated();
        $foto = $request->file("foto");

        $pathFoto = $this->handleFileUpload($foto, "foto", "", "foto_");

        $artikel = Artikel::create([
            "judul" => $data["judul"],
            "foto" => $pathFoto,
            "isi" => $data["isi"],
            "is_mobile" => $data["on_mobile"],
            "kategori" => $data["kategori"],
            "penulis" => $admin->id,
            "tanggal" => Carbon::now()
        ]);
        $artikel->with("penulis_admin");

        return new ArtikelResource($artikel); ;
    }

    public function delete(int $artikelId)
    {
        $artikel = Artikel::find($artikelId);
        if (!$artikel) {
            return response()->json(["message" => "artikel tidak ditemukan"], 404);
        }

        Storage::disk("public")->delete($artikel->foto);
        $artikel->delete();
        return response()->json(["message" => "artikel berhasil dihapus"], 200);
    }

    public function update(int $artikelId, ArtikelUpdateRequest $request)
    {
        $artikel = Artikel::with("penulis_admin")->find($artikelId);
        if (!$artikel) {
            return response()->json(["message" => "artikel tidak ditemukan"], 404);
        }

        $data = $request->validated();
        $foto = $request->file("foto");

        if ($foto) {
            $pathFoto = $this->handleFileUpload($foto, "foto", $artikel->foto, "foto_");
            $artikel->foto = $pathFoto;
        }

        $artikel->judul = $data["judul"];
        $artikel->isi = $data["isi"];
        $artikel->kategori = $data["kategori"];
        $artikel->is_mobile = $data["on_mobile"];
        $artikel->save();

        return new ArtikelResource($artikel);
    }

    public function getAllArtikel(Request $request)
    {
        $q = $request->query("q");
        $kategori = $request->query("kategori");
        $size = $request->query("size", 10);

        $artikels = Artikel::with('penulis_admin')
            ->when($q, function ($builder) use ($q) {
                $builder->where("judul", "like", "%$q%");
            })
            ->when($kategori, function ($builder) use ($kategori) {
                $builder->where("kategori", $kategori);
            })
            ->orderBy("created_at", "desc")
            ->paginate($size);


        return ArtikelResource::collection($artikels);
    }

    public function getArtikel(int $artikelId)
    {
        $artikel = Artikel::with("penulis_admin")->find($artikelId);
        if (!$artikel) {
            return response()->json(["message" => "artikel tidak ditemukan"], 404);
        }
        return new ArtikelResource($artikel);
    }

    public function getAllArtikelOnMobile(Request $request){
        $artikel = Artikel::with("penulis_admin")->where("is_mobile", "1")->get();
        return ArtikelResource::collection($artikel);
    }
}
