<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Petshop extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $table = 'petshops';
    protected $fillable = [
        'petshop_name',
        'company_name',
        'phone_number',
        'petshop_email',
        'permit',
        'province',
        'city',
        'district',
        'postal_code',
        'petshop_address',
        'user_id',
    ];
    public function user_id()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
