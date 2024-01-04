<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'start_time', 'end_time', 'date', 'is_visible', 'phone', 'name', 'haircut_types'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}