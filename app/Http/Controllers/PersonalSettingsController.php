<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;

class PersonalSettingsController extends Controller
{
    public function show()
    {
    	$user = auth()->user();

    	$form = [
    		'name' => $user->name
    	];

    	return to_json([
    		'form' => $form
    	]);
    }

    public function update(Request $request)
    {
        $request->validate( [
            'name' => 'required|string',
            'old_password' => 'nullable',
            'new_password' => 'nullable|required_with:old_password|confirmed|between:6,15',
        ]);

        $user = auth()->user();
        $user->name = $request->name;

        if($request->has('old_password')) {
            // change password
            if(!Hash::check($request->old_password, $user->password)) {
                return to_json([
                    'message' => 'The given data was invalid!',
                    'errors' => [
                        'old_password' => [__('lang.invlaid_pass')]
                    ]
                ], 422);
            }

            $user->password = bcrypt($request->new_password);
        }

        $user->save();

        return to_json([
            'saved' => true
        ]);
    }
}
