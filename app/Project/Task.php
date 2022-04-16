<?php

namespace App\Project;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Task extends Model
{
	use Filterable;

    protected $table = 'project_tasks';

    protected $fillable = [
    	'title', 'start_date', 'due_date', 'description',
    	'stage_id'
    ];

    protected $sortable = [
    	'id', 'title', 'start_date', 'due_date', 'created_at'
    ];

    protected $searchable = [
    	'title'
    ];

    protected $allowedFilters = [];

    public function stage()
    {
    	return $this->belongsTo(Stage::class, 'stage_id', 'id');
    }
}
