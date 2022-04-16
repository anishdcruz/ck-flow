<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Type extends Model
{
	use Filterable;

    protected $table = 'contract_types';
    protected $fillable = ['name'];
    protected $sortable = [
    	'id', 'name', 'created_at'
    ];

    protected $searchable = ['name'];

    protected $allowedFilters = [];
}
