<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keterlambatan extends Model
{
    protected $table = "keterlambatans";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        "user_id",
        "matkul",
        "waktu"
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
