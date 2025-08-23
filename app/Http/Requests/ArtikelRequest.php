<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArtikelRequest extends FormRequest
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
            "foto"=>"required|image|mimes:jpeg,png,jpg,gif,svg|max:4096",
            "isi" => "required|string",
            "on_mobile"=>"required|boolean|",
            "kategori" => "required|string|in:Artikel,Galeri",
        ];
    }
}
