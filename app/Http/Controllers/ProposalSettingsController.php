<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Template\Template;
use App\Proposal\Status;
use App\Proposal\Proposal;

class ProposalSettingsController extends Controller
{
    public function show()
    {
        $this->authorize('access', 'settings.proposals');
        $options = [
            'email_variables' => Proposal::emailVariables()
        ];

    	$form = [
    		'proposal_status_on_create_id' => null,
    		'proposal_status_on_create' => null,
		    'proposal_status_on_email_sent_id' => null,
		    'proposal_status_on_email_sent' => null,
		    'default_proposal_template_id' => null,
		    'default_proposal_template' => null,
    		'close_proposal_after_days' => settings()->get('close_proposal_after_days'),
            'proposal_email_template' => settings()->get('proposal_email_template'),
            'proposal_email_subject' => settings()->get('proposal_email_subject')
    	];

    	$id = settings()->get('proposal_status_on_create_id');

    	if($id) {
    		$c = Status::findOrFail($id);
    		$form['proposal_status_on_create'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['proposal_status_on_create_id'] = $id;
    	}

    	$id = settings()->get('proposal_status_on_email_sent_id');

    	if($id) {
    		$c = Status::findOrFail($id);
    		$form['proposal_status_on_email_sent'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['proposal_status_on_email_sent_id'] = $id;
    	}


    	$id = settings()->get('default_proposal_template_id');

    	if($id) {
    		$c = Template::findOrFail($id);
    		$form['default_proposal_template'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_proposal_template_id'] = $id;
    	}

    	return to_json([
    		'form' => $form,
            'options' => $options
    	]);
    }

    public function update(Request $request)
    {
        $this->authorize('access', 'settings.proposals');
        $request->validate([
    		'proposal_status_on_create_id' => 'required|integer',
		    'proposal_status_on_email_sent_id' => 'required|integer',
		    'default_proposal_template_id' => 'required|integer',
    		'close_proposal_after_days' => 'nullable|integer',
            'proposal_email_template' => 'required',
            'proposal_email_subject' => 'required'
        ]);

        settings()->setMany(
        	$request->only([
        		'proposal_status_on_create_id',
        		'proposal_status_on_email_sent_id',
        		'default_proposal_template_id',
        		'close_proposal_after_days',
                'proposal_email_template',
                'proposal_email_subject'
        	])
        );

        return to_json([
            'saved' => true
        ]);
    }

}
