<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petshop extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $fillable = [
        'petshop_name',
        'company_name',
        'owner',
        'phone_number',
        'petshop_email',
        'permit',
        'province',
        'city',
        'district',
        'postal_code',
        'petshop_address',
    ];
}
