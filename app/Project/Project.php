<?php

namespace App\Project;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Contact\Contact;
use App\Proposal\Proposal;

class Project extends Model
{
	use Filterable;

    protected $table = 'projects';

    protected $fillable = [
        'contact_id',
        'title',
        'description',
        'category_id',
        'proposal_id',
        'start_date',
        'estimated_finish_date',
        'deadline_date',
        'actual_start_date',
        'actual_end_date',
        'estimated_cost'
    ];

    protected $sortable = [
        'id', 'number', 'start_date', 'estimated_finish_date', 'deadline_date',
        'actual_start_date', 'actual_end_date', 'progress', 'estimated_cost',
        'cost_consumption', 'actual_cost',
        'created_at'
    ];

    protected $searchable = [
        'number'
    ];

    protected $allowedFilters = [
        'id', 'number', 'start_date', 'estimated_finish_date', 'deadline_date',
        'actual_start_date', 'actual_end_date', 'progress', 'estimated_cost',
        'cost_consumption', 'actual_cost', 'title', 'description',
        'created_at', 'contact.id', 'contact.number', 'contact.name', 'contact.created_at',
        'proposal.number', 'proposal.created_at', 'category.name', 'stage.name'
    ];

    public function tasks()
    {
    	return $this->hasMany(Task::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function proposal()
    {
    	return $this->belongsTo(Proposal::class, 'proposal_id', 'id');
    }

    public function stage()
    {
    	return $this->belongsTo(Stage::class, 'stage_id', 'id');
    }

    public function category()
    {
    	return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function exportable()
    {
        return [
            'id', 'number', 'category.name', 'stage.name', 'start_date', 'estimated_finish_date', 'deadline_date',
            'actual_start_date', 'actual_end_date', 'progress', 'estimated_cost',
            'cost_consumption', 'actual_cost', 'title', 'description',
            'created_at', 'contact.id', 'contact.number', 'contact.name', 'contact.created_at',
            'proposal.number', 'proposal.created_at',
        ];
    }
}
