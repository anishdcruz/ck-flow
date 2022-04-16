<?php

namespace App\Lead;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Lead extends Model
{
	use Filterable;

    protected $table = 'leads';

    protected $fillable  = [
    	'organization', 'person', 'email', 'title', 'department', 'mobile',
    	'phone', 'fax', 'website', 'primary_address', 'other_address'
    ];

    protected $sortable = [
    	'id', 'number', 'person', 'organization', 'created_at'
    ];

    protected $searchable = [
    	'number', 'person', 'organization', 'email', 'mobile', 'fax', 'phone', 'website'
    ];

    protected $allowedFilters = [
    	'id', 'number', 'person', 'organization', 'created_at',
    	'email', 'fax', 'phone', 'website', 'mobile', 'title', 'department',
    	'primary_address', 'other_address',
        'status.name'
    ];

    protected $hidden = ['custom_values'];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function exportable()
    {
        return [
            'id', 'number', 'person', 'organization',
            'email', 'fax', 'phone', 'website', 'mobile', 'title', 'department',
            'primary_address', 'other_address', 'created_at',
            'status.name'
        ];
    }
}
