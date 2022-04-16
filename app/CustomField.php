<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
	protected $fillable = ['name', 'fields'];
    public function getFieldsAttribute($value)
    {
    	return json_decode($value, true);
    }
}
