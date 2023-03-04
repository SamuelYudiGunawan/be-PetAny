<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'book_appointment_id',
        'type',
        'transaction_id',
        'quantity',
        'product_status',
        'gross_amount',
        'payment_type',
        'payment_status',
        'midtrans_token',
        'status_code',
        'json_data',
        'signature_key',
        'payment_url',
    ];

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
