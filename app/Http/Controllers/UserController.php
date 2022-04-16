<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;

class UserController extends Controller
{
	public function search()
	{
	    return to_json([
	        'collection' => User::search()
	    ]);
	}

    public function index()
    {
        $this->authorize('access', 'settings.users');
        return to_json([
            'collection' => User::with(['role'])->filter()
        ]);
    }

    public function create()
    {
        $this->authorize('access', 'settings.users');
        $item = [
            'name' => '',
            'email' => '',
            'password' => '',
            'role' => null,
            'role_id' => null
        ];

        return to_json([
            'form' => $item
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'settings.users');
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|integer|exists:roles,id',
            'password' => 'nullable|min:6'
        ]);

        $item = new User;
        $item->name = $request->name;
        $item->email = $request->email;
        $item->role_id = $request->role_id;
        $item->password = bcrypt($request->password);

        $item->save();

        return to_json([
            'saved' => true,
            'id' => $item->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'settings.users');
        $item = User::with(['role'])->findOrFail($id);

        return to_json([
            'model' => $item
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'settings.users');
        $item = User::with(['role'])->findOrFail($id);

        return to_json([
            'form' => $item
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'settings.users');
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id.',id',
            'role_id' => 'required|integer|exists:roles,id',
            'password' => 'nullable|min:6'
        ]);

        $item = User::findOrFail($id);
        $item->name = $request->name;
        $item->email = $request->email;
        $item->role_id = $request->role_id;

        if($request->has('password')) {
        	$item->password = bcrypt($request->password);
        }

        $item->save();

        return to_json([
            'saved' => true,
            'id' => $item->id
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'settings.users');
        $model = User::findOrFail($id);

        // cannot delete self
        if(auth()->id() == $model->id) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->metrics()->delete();
        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
