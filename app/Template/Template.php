<?php

namespace App\Template;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Template extends Model
{

    use Filterable;

    protected $table = 'templates';

    protected $fillable = [
        'name', 'type_id', 'page_size', 'orientation', 'header_height', 'footer_height',
        'header_html', 'header_content_fields', 'stylesheet',
        'footer_html', 'footer_html_fields'
    ];

    protected $sortable = [
    	'id', 'name', 'created_at'
    ];

    protected $searchable = [
        'name'
    ];


    protected $allowedFilters = [
    	'id', 'created_at', 'name', 'type_id'
    ];

    protected $appends = ['type'];

    public function getTypeAttribute()
    {
        $types = [
            '1' => __('lang.proposal'),
            '2' => __('lang.contract'),
            '3' => __('lang.invoice'),
            '4' => __('lang.payment'),
            '5' => __('lang.expense')
        ];

        $id = isset($this->attributes['type_id'])
            ? $this->attributes['type_id']
            : '1';
        return $types[$id];
    }

    public function pages()
    {
        return $this->hasMany(Page::class, 'template_id', 'id')
            ->orderBy('index');
    }

    public function getHeaderContentFieldsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getFooterContentFieldsAttribute($value)
    {
        return json_decode($value, true);
    }
}
