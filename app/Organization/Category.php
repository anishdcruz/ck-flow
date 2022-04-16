<?php

namespace App\Organization;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Category extends Model
{
	use Filterable;

    protected $table = 'organization_categories';

    protected $fillable = ['name'];

    protected $sortable = [
    	'id', 'name', 'created_at'
    ];

    protected $searchable = [
        'name'
    ];

    protected $allowedFilters = [
    	'id', 'name', 'created_at'
    ];
}
