<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Response;

class Report extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi massal (kecuali id)
    protected $fillable = [
    'user_id', 'title', 'description', 'location', 'latitude',
    'longitude', 'image', 'status'
    ];

    // Relasi: laporan ini milik satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- TAMBAHKAN BAGIAN INI ---
 // Relasi ke Response (Tanggapan)
 // Satu laporan bisa memiliki banyak tanggapan
    public function responses()
    {
        return $this->hasMany(Response::class);
    }

}