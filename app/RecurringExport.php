<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class RecurringExport extends Model
{
	use Filterable;

    protected $fillable = [
        'email_to', 'frequency', 'send_on', 'send_at', 'name'
    ];

    protected $sortable = [
    	'id', 'created_at'
    ];

    protected $searchable = [
    ];

    protected $allowedFilters = [
    	'id', 'email_to', 'frequency', 'send_on', 'send_at', 'name', 'created_at'
    ];
}
