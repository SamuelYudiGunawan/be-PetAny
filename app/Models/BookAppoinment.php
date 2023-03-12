<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookAppoinment extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['sortable_date'];

    public function getSortableDateAttribute()
    {
        // Parse the date string and extract the day of the week and date parts
        $parts = explode(', ', $this->date);
        $day_of_week = $parts[0];
        $date_str = $parts[1];

        // Convert the date to a sortable format
        $date = DateTime::createFromFormat('j M', $date_str);
        return $date->format('Y-m-d');
    }
    public function user_id()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
