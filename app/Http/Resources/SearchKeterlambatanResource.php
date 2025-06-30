<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchKeterlambatanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'matkul' => $this->matkul,
            'waktu' => $this->waktu
                ? \Carbon\Carbon::parse($this->waktu)->format('Y-m-d H:i')
                : null,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'name' => $this->user->name,
                    'jurusan' => $this->user->jurusan,
                    'angkatan' => $this->user->angkatan,
                ];
            }),
        ];
    }
}
