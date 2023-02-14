<?php

namespace App\Models;

use App\Models\Petshop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ['id'];
    public function petshop_id()
    {
        return $this->belongsTo(Petshop::class, 'petshop_id');
    }
}
