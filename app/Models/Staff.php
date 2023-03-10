<?php

namespace App\Models;

use App\Models\User;
use App\Models\Petshop;
use App\Models\JamOperasionalDokter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'petshop_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function petshop_id()
    {
        return $this->belongsTo(Petshop::class);
    }

    public function jam_operasional()
    {
        return $this->hasMany(JamOperasionalDokter::class, 'user_id', 'user_id');
    }
}
