<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    // Tambahkan baris ini agar nama_lapangan dan harga_per_jam bisa disimpan
    protected $fillable = [
        'nama_lapangan',
        'harga_per_jam'
    ];
}