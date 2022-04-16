<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class UserInvite extends Model
{
	use Filterable;

	protected $sortable = [
		'id', 'email', 'name', 'created_at'
	];

	protected $searchable = [];

	protected $allowedFilters = [
		'id', 'email', 'name', 'token', 'created_at'
	];

    protected $table = 'user_invites';

    public function role()
    {
    	return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
