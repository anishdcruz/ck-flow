<?php

namespace App\Opportunity;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Source extends Model
{
	use Filterable;

    protected $table = 'opportunity_sources';
    protected $fillable = ['name'];
    protected $sortable = [
    	'id', 'name', 'created_at'
    ];

    protected $searchable = ['name'];

    protected $allowedFilters = [];

}
