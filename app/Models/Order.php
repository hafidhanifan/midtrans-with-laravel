<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_id', 'user_id', 'amount', 'status', 'payment_type', 'midtrans_transaction_id', 'raw_response'];

    protected $casts = [
        'raw_response' => 'array',
    ];
}