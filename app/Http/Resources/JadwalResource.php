<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JadwalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "senin_1" => $this->senin_1,
            "senin_2" => $this->senin_2,
            "selasa_1" => $this->selasa_1,
            "selasa_2" => $this->selasa_2,
            "rabu_1" => $this->rabu_1,
            "rabu_2" => $this->rabu_2,
            "kamis_1" => $this->kamis_1,
            "kamis_2" => $this->kamis_2,
            "jumat_1" => $this->jumat_1,
            "jumat_2" => $this->jumat_2
        ];
    }
}
