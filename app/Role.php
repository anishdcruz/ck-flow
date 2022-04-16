<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Role extends Model
{
    use Filterable;

    protected $sortable = ['id', 'name', 'created_at'];

    protected $fillable = [
        'name', 'description', 'permissions'
    ];
    protected $searchable = ['name'];

    protected $allowedFilters = [
    	'id', 'name', 'description', 'created_at'
    ];

    public function getPermissionsAttribute($value)
    {
    	return json_decode($value, true);
    }
}
