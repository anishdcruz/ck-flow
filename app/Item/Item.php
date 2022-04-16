<?php

namespace App\Item;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Item extends Model
{
	use Filterable;

    protected $table = 'items';

    protected $fillable = [
    	'description', 'uom_id', 'category_id', 'unit_price'
    ];

    protected $sortable = [
    	'id', 'code', 'description', 'unit_price',
    	'created_at'
    ];

    protected $searchable = [
        'code'
    ];

    protected $allowedFilters = [
    	'id', 'code', 'description', 'unit_price',
        'category.name', 'uom.name', 'created_at'
    ];

    protected $hidden = ['custom_values'];

    public function category()
    {
    	return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function uom()
    {
    	return $this->belongsTo(Uom::class, 'uom_id', 'id');
    }

    public function exportable()
    {
        return [
            'id', 'code', 'description', 'unit_price',
            'category.name', 'uom.name', 'created_at'
        ];
    }
}
