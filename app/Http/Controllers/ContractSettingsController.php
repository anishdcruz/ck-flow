<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Template\Template;
use App\Contract\Status;
use App\Contract\Type;
use App\Contract\Contract;

class ContractSettingsController extends Controller
{
    public function show()
    {
        $this->authorize('access', 'settings.contracts');
        $options = [
            'email_variables' => Contract::emailVariables()
        ];
    	$form = [
    		'contract_status_on_email_sent_id' => null,
    		'contract_status_on_email_sent' => null,
    		'contract_status_on_create_id' => null,
    		'contract_status_on_create' => null,
		    'default_contract_template_id' => null,
		    'default_contract_template' => null,
		    'default_contract_type_id' => null,
		    'default_contract_type' => null,
            'contract_email_template' => settings()->get('contract_email_template'),
            'contract_email_subject' => settings()->get('contract_email_subject')
    	];

    	$id = settings()->get('contract_status_on_create_id');

    	if($id) {
    		$c = Status::findOrFail($id);
    		$form['contract_status_on_create'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['contract_status_on_create_id'] = $id;
    	}

    	$id = settings()->get('contract_status_on_email_sent_id');

    	if($id) {
    		$c = Status::findOrFail($id);
    		$form['contract_status_on_email_sent'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['contract_status_on_email_sent_id'] = $id;
    	}


    	$id = settings()->get('default_contract_template_id');

    	if($id) {
    		$c = Template::findOrFail($id);
    		$form['default_contract_template'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_contract_template_id'] = $id;
    	}

    	$id = settings()->get('default_contract_type_id');

    	if($id) {
    		$c = Type::findOrFail($id);
    		$form['default_contract_type'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_contract_type_id'] = $id;
    	}

    	return to_json([
    		'form' => $form,
            'options' => $options
    	]);
    }

    public function update(Request $request)
    {
        $this->authorize('access', 'settings.contracts');
        $request->validate([
    		'contract_status_on_create_id' => 'required|integer',
		    'contract_status_on_email_sent_id' => 'required|integer',
		    'default_contract_template_id' => 'required|integer',
		    'default_contract_type_id' => 'required|integer',
            'contract_email_template' => 'required',
            'contract_email_subject' => 'required'
        ]);

        settings()->setMany(
        	$request->only([
        		'contract_status_on_create_id',
        		'contract_status_on_email_sent_id',
        		'default_contract_template_id',
        		'default_contract_type_id',
                'contract_email_template',
                'contract_email_subject'
        	])
        );

        return to_json([
            'saved' => true
        ]);
    }

}
