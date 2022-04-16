<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $hidden = [
    	'notable_id', 'notable_type'
    ];

    public function notable()
    {
        return $this->morphTo();
    }
}
