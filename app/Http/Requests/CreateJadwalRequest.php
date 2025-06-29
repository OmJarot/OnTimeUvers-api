<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJadwalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->level == "dba";
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "senin_1" => ["max:100"],
            "senin_2" => ["max:100"],
            "selasa_1" => ["max:100"],
            "selasa_2" => ["max:100"],
            "rabu_1" => ["max:100"],
            "rabu_2" => ["max:100"],
            "kamis_1" => ["max:100"],
            "kamis_2" => ["max:100"],
            "jumat_1" => ["max:100"],
            "jumat_2" => ["max:100"],
        ];
    }
}
