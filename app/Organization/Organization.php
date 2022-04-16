<?php

namespace App\Organization;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Organization extends Model
{
	use Filterable;

    protected $table = 'organizations';

    protected $fillable = [
        'name', 'organization_category_id',
        'phone', 'fax', 'email','website',
        'primary_address', 'other_address',
    ];

    protected $sortable = [
    	'id', 'number', 'name',
    	'created_at'
    ];

    protected $searchable = [
        'number', 'name', 'email', 'fax', 'phone', 'website'
    ];

    protected $allowedFilters = [
    	'id', 'number', 'name',
        'phone', 'fax', 'email','website',
    	'primary_address', 'other_address',
    	'total_revenue', 'amount_receivable', 'created_at',
    	// category
        'category.name'
    ];

    protected $hidden = ['custom_values'];

    public function category()
    {
    	return $this->belongsTo(Category::class, 'organization_category_id','id');
    }

    public function exportable()
    {
        return [
            'id', 'number', 'name', 'category.name',
            'phone', 'fax', 'email','website',
            'primary_address', 'other_address',
            'created_at'
        ];
    }

    public static function templateVariables($key, $show = true)
    {
        $base = [
            'id', 'number',
            'name', 'title', 'department',
            'phone', 'fax', 'email','website',
            'primary_address', 'other_address',
        ];

        $b = collect($base)->map(function($item) use ($key) {
            if(is_null($key)) {
                return $item;
            }
            return $key.'.'.$item;
        });

        if($show) {

            $cf = custom_fields_names('organizations');
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

    public function customFields($key)
    {
        return custom_fields_to_array(
            'organizations',
            $key,
            $this->attributes['custom_values']
        );
    }
}
