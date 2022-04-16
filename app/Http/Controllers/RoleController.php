<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Services\Permission;
use DB;

class RoleController extends Controller
{
	public function search()
	{
	    return to_json([
	        'collection' => Role::search()
	    ]);
	}

    public function index()
    {
        $this->authorize('access', 'settings.roles_and_permission');
        return to_json([
            'collection' => Role::filter()
        ]);
    }

    public function create()
    {
        $this->authorize('access', 'settings.roles_and_permission');
        $item = [
            'name' => '',
            'description' => '',
            'permissions' => Permission::schema()
        ];

        return to_json([
            'form' => $item
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'settings.roles_and_permission');
        $request->validate([
            'name' => 'required|unique:roles,name',
            'description' => 'required|string',
            'permissions' => 'required'
        ]);

        $item = new Role;
        $item->name = $request->name;
        $item->description = $request->description;
        $item->permissions = json_encode($request->permissions);
        $item->save();

        return to_json([
            'saved' => true,
            'id' => $item->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'settings.roles_and_permission');
        $item = Role::findOrFail($id);

        return to_json([
            'model' => $item
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'settings.roles_and_permission');
        $item = Role::findOrFail($id);

        return to_json([
            'form' => $item
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'settings.roles_and_permission');
        $request->validate([
            'name' => 'required|unique:roles,name,'.$id.',id',
            'description' => 'required|string',
            'permissions' => 'required'
        ]);

        $item = Role::findOrFail($id);
        $item->name = $request->name;
        $item->description = $request->description;
        $item->permissions = json_encode($request->permissions);
        $item->save();

        return to_json([
            'saved' => true,
            'id' => $item->id
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'settings.roles_and_permission');
        $model = Role::findOrFail($id);

        if(DB::table('users')->where('role_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('user_invites')->where('role_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }

}
