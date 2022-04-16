<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense\Category;
use App\Template\Template;

class ExpenseSettingsController extends Controller
{
    public function show()
    {
        $this->authorize('access', 'settings.expenses');
    	$form = [
    		'default_expense_category_id' => null,
    		'default_expense_category' => null,
            // 'default_expense_template_id' => null,
            // 'default_expense_template' => null
    	];

        // $id = settings()->get('default_expense_template_id');

        // if($id) {
        //     $c = Template::findOrFail($id);
        //     $form['default_expense_template'] = [
        //         'name' => $c->name,
        //         'id' => $c->id
        //     ];
        //     $form['default_expense_template_id'] = $id;
        // }

    	$id = settings()->get('default_expense_category_id');

    	if($id) {
    		$c = Category::findOrFail($id);
    		$form['default_expense_category'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_expense_category_id'] = $id;
    	}

    	return to_json([
    		'form' => $form
    	]);
    }

    public function update(Request $request)
    {
        $this->authorize('access', 'settings.expenses');
        $request->validate([
    		'default_expense_category_id' => 'required|integer',
            // 'default_expense_template_id' => 'required|integer'
        ]);

        settings()->setMany(
        	$request->only([
        		'default_expense_category_id',
                // 'default_expense_template_id'
        	])
        );

        return to_json([
            'saved' => true
        ]);
    }
}
