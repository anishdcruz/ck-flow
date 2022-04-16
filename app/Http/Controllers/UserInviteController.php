<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\UserInvite;
use App\User;
use Mail;
use App\Mail\SendSimpleMail;
use DB;

class UserInviteController extends Controller
{
    public function index()
    {
        $this->authorize('access', 'settings.invitations');
        return to_json([
            'collection' => UserInvite::filter()
        ]);
    }

    public function create()
    {
        $this->authorize('access', 'settings.invitations');
        $item = [
        	'name' => '',
            'email' => '',
            'role' => null,
            'role_id'  => null
        ];

        return to_json([
            'form' => $item
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'settings.invitations');
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|integer'
        ]);

        $item = new UserInvite;
        $item->name = $request->name;
        $item->email = $request->email;
        $item->token =  (string) Str::uuid();
        $item->role_id = $request->role_id;
        $item->save();


        // todo sent email

        $info = [
            'email_to' => $item->email,
            'bcc' => [],
            'subject' => '',
            'message' => ''
        ];

        $vars = [
            'register_url',
            'name'
        ];

        $d = [
            'register_url' => url('invite/'.$item->token),
            'name' => $item->name
        ];

        $info['subject'] = parseSimpleTemplate(settings()->get('user_invite_email_subject'), $vars, $d);
        $info['message'] = parseSimpleTemplate(settings()->get('user_invite_email_template'), $vars, $d);

        Mail::send(new SendSimpleMail($info));


        return to_json([
            'saved' => true,
            'id' => $item->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'settings.invitations');
        $item = UserInvite::with('role')->findOrFail($id);

        return to_json([
            'model' => $item
        ]);
    }

    public function register($id)
    {
    	$item = UserInvite::where('token', $id)
    		->firstOrFail();

    	return view('register', ['item' => $item]);
    }

    public function saveUser($id, Request $request)
    {
    	$item = UserInvite::where('token', $id)
    		->firstOrFail();

    	$request->validate([
    		'name' => 'required',
    		'password' => 'required|min:6|confirmed'
    	]);


    	$user = new User;
    	$user->name = $request->name;
    	$user->email = $item->email;
    	$user->role_id = $item->role_id;
    	$user->password = bcrypt($request->password);

    	$user->save();

    	$item->delete();

    	return redirect('/')
    		->with('message', __('lang.success_registed'));
    }

    public function destroy($id)
    {
        $this->authorize('access', 'settings.invitations');
        $model = UserInvite::findOrFail($id);

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
