<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArtikelUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "judul" => "required|string",
            "foto"=>"image|mimes:jpeg,png,jpg,gif,svg|max:4096",
            "on_mobile"=>"required|boolean",
            "isi" => "required|string",
            "kategori" => "required|string|in:Artikel,Galeri",
        ];
    }
}
