<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Status extends Model
{
	use Filterable;

    protected $table = 'contract_statuses';

    protected $fillable = ['name', 'color', 'locked'];

    protected $sortable = [
    	'id', 'name', 'created_at'
    ];

    protected $searchable = [
    	'name'
    ];

    protected $allowedFilters = [];
}
