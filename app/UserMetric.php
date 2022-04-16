<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMetric extends Model
{
	protected $fillable = [
		'card_label',
		'filter_match',
		'resource',
		'params',
		'model',
		'metric_type',
		'group_by',
		'chart_type',
		'time_peroid',
		'color'
	];
}
