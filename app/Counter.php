<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
	protected $appends = ['number'];

	public function getNumberAttribute()
	{
	    return $this->attributes['prefix'].$this->attributes['value'];
	}
}
