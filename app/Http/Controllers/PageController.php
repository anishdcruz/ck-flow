<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class PageController extends Controller
{
    public function index()
    {
    	if(!Auth::check()) {
            return view('index');
        }
    	return view('app');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255'
        ]);

        $auth = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);

        if($auth) {
            return redirect()
                ->intended('/');
        }

        return back()
            ->withInput()
            ->withErrors(['email' => [__('lang.invalid_login')]]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
