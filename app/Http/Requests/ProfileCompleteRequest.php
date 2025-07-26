<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileCompleteRequest extends FormRequest
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
            "tanggal_lahir" => "required|date|date_format:Y-m-d|before:today",
            "jenis_kelamin" => "required|in:Laki-laki,Perempuan",
            "status_nikah" => "required|boolean",
            "ktp" => "file|image|max:10240",
            "profile"=>"file|mimes:jpg,jpeg,png|max:10240",
        ];
    }
}
