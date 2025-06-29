<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    protected $table = "jadwals";
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        "id",
        "senin_1",
        "senin_2",
        "selasa_1",
        "selasa_2",
        "rabu_1",
        "rabu_2",
        "kamis_1",
        "kamis_2",
        "jumat_1",
        "jumat_2",
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, "id", "id");
    }
}
