<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Support\Filterable;

class User extends Authenticatable
{
    use Filterable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $sortable = [
        'id', 'created_at'
    ];

    protected $searchable = [
        'name', 'email'
    ];

    protected $allowedFilters = [
        'id', 'created_at', 'name', 'email', 'role.name'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function metrics()
    {
        return $this->hasMany(UserMetric::class, 'user_id', 'id');
    }

    // todo cache
    public function permissions()
    {
        $items = [];

        foreach($this->role->permissions as $group) {
            foreach($group['actions'] as $key => $value) {
                if($value) {
                    $items[] = $group['name'].'.'.$key;
                }
            }
        }

        return $items;
    }

    // todo cache
    public function topLinks()
    {
        $items = [
            'overview'
        ];

        foreach($this->role->permissions as $group) {
            if($group['name'] === 'settings') {
                foreach($group['actions'] as $key => $value) {
                    if($value) {
                        $items[] = $group['name'];
                    }
                }
            } else {
                if(isset($group['actions']['index']) && !$group['actions']['index']) {
                    // not allowed
                } else {
                    $items[] = $group['name'];
                }
            }

        }

        return $items;
    }
}
