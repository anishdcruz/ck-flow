<?php

namespace App\Opportunity;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Stage extends Model
{
	use Filterable;

    protected $table = 'opportunity_stages';
    protected $fillable = ['name', 'color', 'locked'];
    protected $sortable = [
    	'id', 'name', 'created_at'
    ];

    protected $searchable = ['name'];

    protected $allowedFilters = [];
}
