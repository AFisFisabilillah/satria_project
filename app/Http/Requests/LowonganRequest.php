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
            "min_gaji"=>"required|int|min:0",
            "max_gaji"=>"required|int|gt:min_gaji",
            "currency"=>"required|string",
            "kuota_lowongan"=>"required|int",
            "sip2mi"=>"required|string",
            "batas_waktu"=>"required|date|date_format:Y-m-d",
        ];
    }
}
