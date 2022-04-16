<?php

namespace App\Vendor;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Expense\Expense;

class Vendor extends Model
{
	use Filterable;

    protected $table = 'vendors';

    protected $fillable = [
        'name', 'mobile', 'phone', 'fax', 'email','website',
        'primary_address', 'other_address', 'category_id'
    ];

    protected $sortable = [
    	'id', 'number', 'name', 'total_expense',
    	'created_at'
    ];

    protected $searchable = [
        'number', 'name', 'email', 'fax', 'phone', 'website', 'mobile'
    ];

    protected $allowedFilters = [
    	'id', 'number', 'name', 'mobile', 'phone', 'fax', 'email',
    	'website', 'primary_address', 'other_address',
        'total_expense', 'created_at',
        'category.name'
    ];

    protected $hidden = ['custom_values'];

    public function category()
    {
    	return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'vendor_id', 'id');
    }

    public function exportable()
    {
        return [
            'id', 'number', 'name', 'mobile', 'phone', 'fax', 'email',
            'website', 'primary_address', 'other_address',
            'total_expense', 'category.name', 'created_at',
        ];
    }

    public static function templateVariables($key, $show = true)
    {
        $base = [
            'id', 'number', 'name', 'mobile', 'phone', 'fax', 'email',
            'website', 'primary_address', 'other_address',
            'category.name'
        ];

        $b = collect($base)->map(function($item) use ($key) {
            if(is_null($key)) {
                return $item;
            }
            return $key.'.'.$item;
        });

        if($show) {

            $cf = custom_fields_names('vendors');
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
