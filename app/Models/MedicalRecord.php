<?php

namespace App\Models;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $table = 'medical_records';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'title',
        'description',
        'treatment',
        'date',
        'attachment',
        'pet_id',
    ];
    
    public function pet_id()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }
}
