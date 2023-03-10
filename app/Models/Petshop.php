<?php

namespace App\Models;

use App\Models\User;
use App\Models\Staff;
use App\Models\Product;
use App\Models\JamOperasional;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Petshop extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $table = 'petshops';
    protected $guarded = ['id'];
    public function user_id()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function product()
    {
        return $this->hasMany(Product::class);
    }
    public function jamOperasional()
    {
        return $this->hasMany(JamOperasional::class);
    }
    public function staffs()
    {
        return $this->hasMany(Staff::class);
    }
    public function scopeStatus($query, $value)
    {
        return $query->where('status', $value);
    }
}
