<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'field_id',
        'jam_mulai',
        'jam_selesai',
        'total_harga',
        'status'
    ];

        public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
