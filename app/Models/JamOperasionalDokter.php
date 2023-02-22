<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JamOperasionalDokter extends Model
{
    use HasFactory;

    protected $fillable = [
        'hari_buka',
        'is_open',
        'jam_buka',
        'jam_tutup',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
