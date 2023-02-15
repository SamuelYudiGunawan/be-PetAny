<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamOperasional extends Model
{
    use HasFactory;

    protected $fillable = [
        'hari_buka',
        'is_open',
        'jam_buka',
        'jam_tutup',
        'petshop_id'
    ];

    public function petshop()
    {
        return $this->belongsTo(Petshop::class);
    }
}
