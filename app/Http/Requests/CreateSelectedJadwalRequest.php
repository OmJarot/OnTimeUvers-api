<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSelectedJadwalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "id" => ["required", "array"],
            "id.*" => ["string", "max:20"],
            "jadwal" => ["nullable", "array"],
            "jadwal.senin_1" => ["nullable", "string", "max:100"],
            "jadwal.senin_2" => ["nullable", "string", "max:100"],
            "jadwal.selasa_1" => ["nullable", "string", "max:100"],
            "jadwal.selasa_2" => ["nullable", "string", "max:100"],
            "jadwal.rabu_1" => ["nullable", "string", "max:100"],
            "jadwal.rabu_2" => ["nullable", "string", "max:100"],
            "jadwal.kamis_1" => ["nullable", "string", "max:100"],
            "jadwal.kamis_2" => ["nullable", "string", "max:100"],
            "jadwal.jumat_1" => ["nullable", "string", "max:100"],
            "jadwal.jumat_2" => ["nullable", "string", "max:100"],
        ];
    }
}
