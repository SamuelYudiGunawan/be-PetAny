<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'user_id',
    //     'product_id',
    //     'book_appoinment_id',
    //     'type',
    //     'gross_amount',
    //     'midtrans_token',
    //     'transaction_id',
    //     'status',
    // ];
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function bookAppointment()
    {
        return $this->belongsTo(BookAppointment::class);
    }
}
