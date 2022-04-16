<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Image extends Model
{
    use Filterable;

    protected $fillable = [
    	'title', 'filename', 'size', 'extension', 'dimension'
    ];

    protected $sortable = [
    	'id', 'size', 'extension', 'created_at'
    ];

    protected $searchable = [];

    protected $allowedFilters = [
    	'id', 'title', 'filename', 'size', 'extension', 'dimension', 'created_at'
    ];
}
