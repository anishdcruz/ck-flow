<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Note;
use App\Contact\Contact;

class NoteController extends Controller
{
    // public function store($id, Request $request)
    // {
    // 	$request->validate([
    // 		'note' => 'required',
    // 		'type' => 'required|in:contacts' // todo: moree
    // 	]);
    // 	sleep(2);
    // 	$note = new Note;
    // 	$note->user_id = 1; // todo: change later
    // 	$note->description = $request->note;

    // 	switch ($request->type) {
    // 		case 'contacts':
    // 			$type = Contact::findOrFail($id);
    // 			$type->notes()
    // 				->save($note);
    // 			break;

    // 		default:
    // 			abort('Invalid Note Type!');
    // 			break;
    // 	}

    // 	return to_json([
    // 		'saved' => true,
    // 		'model' => $note
    // 	]);
    // }
}
