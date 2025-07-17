<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PelamarRegisterRequest extends FormRequest
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
            "nama" => "required|string|max:100|min:3",
            "email"=>"required|string|email|max:100|unique:pelamars,email_pelamar",
            "password"=>"required|string|min:8",
            'telp' => ["required", "string", "max:15", "regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/", "unique:pelamars,telp_pelamar"],
            "domisili"=>"required|string|max:100|min:3"
        ];
    }
}
