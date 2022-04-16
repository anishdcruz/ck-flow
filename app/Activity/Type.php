<?php

namespace App\Activity;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Type extends Model
{
	use Filterable;

    protected $table = 'activity_types';

    protected $sortable = [];

    protected $searchable = ['name'];

    protected $allowedFilters = [];

}
