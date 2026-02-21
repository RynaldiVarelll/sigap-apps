<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Response;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    //Relasi: Tanggapan ini ditulis oleh satu User (admin)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relasi: Tanggapan ini milik laporan yang mana
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}