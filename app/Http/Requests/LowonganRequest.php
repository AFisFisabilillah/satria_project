<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LowonganRequest extends FormRequest
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
            "nama"=>"required|string",
            "deskripsi"=>"required|string",
            "syarat"=>"required|string",
            "negara"=>"required|string",
            "posisi"=>"required|string",
            "gaji"=>"required|int",
            "deadline"=>"required|date_format:Y-m-d H:i|after:now",
            "kontrak"=>"required|string",
            "lokasi"=>"required|string",
            "currency"=>"required|string",
            "jumlah_lowongan"=>"required|int",
        ];
    }
}
