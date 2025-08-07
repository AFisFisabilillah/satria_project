<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PelamarRequest extends FormRequest
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
            "nama" => "required|string|max:255",
            "email"=>"required|email|unique:pelamars,email_pelamar|max:255",
            "telp" => ["required", "string", "max:255", "regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/"],
            "tanggal_lahir" => "required|date|date_format:Y-m-d|before:today",
            "domisili" => ["required", "string", "max:255"],
            "status_nikah" => ["required", "boolean", "max:255"],
            "profile" => ["required", "file", "mimes:jpg,png,jpeg", "max:2048"],
            "ktp"=>["required", "file", "mimes:jpg,png,jpeg", "max:2048"],
            "password" => ["required", "string", "min:8", "max:255"],
            "gender" => ["required", "string", "max:255", "in:Laki-laki,Perempuan"],
        ];
    }
}
