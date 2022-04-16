<?php

namespace App\Activity;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

    protected $fillable = ['callable_id', 'callable_type', 'type_id', 'description', 'date'];

    public function type()
    {
    	return $this->belongsTo(Type::class, 'type_id', 'id');
    }
}
