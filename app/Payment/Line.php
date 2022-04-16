<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Invoice\Invoice;

class Line extends Model
{
    protected $table = 'payment_lines';

    protected $fillable = [
    	'invoice_id', 'amount_applied'
    ];

    public function invoice()
    {
    	return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
