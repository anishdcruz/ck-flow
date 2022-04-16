<?php

namespace App\Opportunity;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Contact\Contact;

class Opportunity extends Model
{
	use Filterable;

    protected $table = 'opportunities';

    protected $fillable = [
        'title',
        'description',
        'probability',
        'start_date',
        'close_date',
        'source_id',
        'category_id',
        'stage_id',
        'value',
        'contact_id'
    ];

    protected $sortable = [
    	'id', 'number', 'start_date', 'close_date', 'value',
    	'probability', 'created_at', 'value'
    ];

    protected $searchable = [
    	'number', 'title'
    ];

    protected $allowedFilters = [
    	'id', 'number', 'title', 'description', 'probability', 'value',
    	'start_date', 'close_date', 'status_id', 'created_at',
    	'contact.id', 'contact.name', 'contact.number', 'contact.created_at',
    	'source.name', 'category.name', 'stage.name'
    ];

    public function category()
    {
    	return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function source()
    {
    	return $this->belongsTo(Source::class, 'source_id', 'id');
    }

    public function stage()
    {
    	return $this->belongsTo(Stage::class, 'stage_id', 'id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    protected function exportable()
    {
        return [
            'id', 'number', 'title', 'description', 'probability', 'value',
            'start_date', 'close_date', 'created_at',
            'contact.name', 'contact.number', 'contact.created_at',
            'source.name', 'category.name', 'stage.name', 'status_id'
        ];
    }

    public static function templateVariables($key, $show = true)
    {
        $base = [
            'id', 'number', 'title', 'description', 'probability', 'value',
            'start_date', 'close_date',
        ];

        $b = collect($base)->map(function($item) use ($key) {
            if(is_null($key)) {
                return $item;
            }
            return $key.'.'.$item;
        });

        if($show) {

            $cf = custom_fields_names('opportunities');
            $c = collect($cf)->map(function($item) use ($key) {
                if(is_null($key)) {
                    return $item;
                }
                return $key.'.'.$item;
            });

            $f = $b->merge($c);
            return $f->toArray();
        }


        return $b->toArray();
    }
}
