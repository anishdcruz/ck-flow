<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'payment_tokens';

    protected $fillable = ['request_id', 'value', 'type', 'gateway', 'other', 'email'];
}
