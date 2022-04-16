<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Method extends Model
{
	use Filterable;

    protected $table = 'payment_methods';
    protected $fillable = ['name'];
    protected $sortable = [
    	'id', 'name', 'created_at'
    ];

    protected $searchable = ['name'];

    protected $allowedFilters = [];
}
