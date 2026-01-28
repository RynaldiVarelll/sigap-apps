<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi massal (kecuali id)
    protected $guarded = ['id'];

    // Relasi: laporan ini milik satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
