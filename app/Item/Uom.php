<?php

namespace App\Item;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Uom extends Model
{
	use Filterable;

    protected $table = 'item_uoms';

    protected $fillable = ['name'];

    protected $sortable = [
        'id', 'created_at', 'name'
    ];

    protected $searchable = [
        'name'
    ];

    protected $allowedFilters = [

    ];
}
