<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Deposit extends Model
{
	use Filterable;

    protected $table = 'payment_deposits';
    protected $fillable = ['name'];
    protected $sortable = [
    	'id', 'name', 'created_at'
    ];

    protected $searchable = ['name'];

    protected $allowedFilters = [];
}
