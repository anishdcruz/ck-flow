<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Activity\Activity;

class ActivityController extends Controller
{
    // public function index(Request $request)
    // {
    // 	$collection = Activity::with('type')->where([
    // 		'callable_type' => $request->type,
    // 		'callable_id' => $request->id
    // 	])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate($request->get('limit', 5));

    // 	return to_json([
    // 		'collection' => $collection
    // 	]);
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'callable_type' => 'required|in:contacts', // todo more
    //         'callable_id' => 'required|integer',
    //         'type_id' => 'required|exists:activity_types,id',
    //         'date' => 'required|date_format:Y-m-d',
    //         'description' => 'required'
    //     ]);

    //     $act = new Activity($request->all());
    //     $act->user_id = 1;
    //     $act->save();

    //     return to_json([
    //         'saved' => true
    //     ]);
    // }

    // public function destroy($id)
    // {
    //     $activity = Activity::findOrFail($id);
    //     $activity->delete();

    //     return to_json([
    //         'deleted' => true
    //     ]);
    // }
}
