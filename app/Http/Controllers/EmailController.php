<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proposal\Proposal;
use App\Contract\Contract;
use App\Invoice\Invoice;
use App\Payment\Payment;
use App\Mail\SendDocument;
use Mail;

class EmailController extends Controller
{
    public function compose(Request $request)
    {
        $request->validate([
            'type' => 'required|in:invoice,proposal,payment,contract',
            'id' => 'required|integer'
        ]);

        $this->authorize('access', $request->type.'s.send_email');

        $warning = null;
        $form = [
            'email_to' => null,
            'bcc' => null,
            'subject' => null,
            'message' => null
        ];

        switch ($request->type) {
            case 'proposal':
                $p = Proposal::with(['contact.organization'])
                    ->findOrFail($request->id);

                $form['email_to'] = $p->contact->email;
                $vars = Proposal::emailVariables();

                $form['subject'] = parseEmailTemplate(settings()->get('proposal_email_subject'), $vars, $p, 'proposals');
                $form['message'] = parseEmailTemplate(settings()->get('proposal_email_template'), $vars, $p, 'proposals');
                break;

            case 'contract':
                $p = Contract::with(['contact.organization', 'proposal'])
                    ->findOrFail($request->id);

                $form['email_to'] = $p->contact->email;
                $vars = Contract::emailVariables();

                $form['subject'] = parseEmailTemplate(settings()->get('contract_email_subject'), $vars, $p, 'contracts');
                $form['message'] = parseEmailTemplate(settings()->get('contract_email_template'), $vars, $p, 'contracts');
                break;

            case 'invoice':
                $p = Invoice::with(['status:id,name,color', 'proposal',
                    'contract', 'contact.organization'])
                    ->findOrFail($request->id);

                $form['email_to'] = $p->contact->email;
                $vars = Invoice::emailVariables();

                $form['subject'] = parseEmailTemplate(settings()->get('invoice_email_subject'), $vars, $p, 'invoices');
                $form['message'] = parseEmailTemplate(settings()->get('invoice_email_template'), $vars, $p, 'invoices');
                break;

            case 'payment':
                $p = Payment::with([
                    'contact.organization', 'deposit:id,name', 'method:id,name',
                    'lines.invoice'
                ])
                    ->findOrFail($request->id);

                $form['email_to'] = $p->contact->email;
                $vars = Payment::emailVariables();

                $form['subject'] = parseEmailTemplate(settings()->get('payment_email_subject'), $vars, $p, 'payments');
                $form['message'] = parseEmailTemplate(settings()->get('payment_email_template'), $vars, $p, 'payments');
                break;

            default:
                # code...
                break;
        }

        return to_json([
            'form' => $form,
            'warning' => $warning
        ]);
    }


    public function sent(Request $request)
    {
        $info = $request->validate([
            'type' => 'required|in:invoice,proposal,payment,contract',
            'id' => 'required|integer',
            'email_to' => 'required|email',
            'bcc' => 'nullable|email',
            'subject' => 'required',
            'message' => 'required'
        ]);

        $this->authorize('access', $request->type.'s.send_email');

        switch ($request->type) {
            case 'proposal':
                $p = Proposal::with([
                    'status:id,name,color', 'opportunity',
                    'contact.organization'
                ])->findOrFail($request->id);

                Mail::send(new SendDocument($p, $info, $request->type));

                // change status
                $id = settings()->get('proposal_status_on_email_sent_id');

                if($id) {
                    $p->status_id = $id;
                    $p->save();
                }
                break;

            case 'contract':
                $p = Contract::with([
                     'status:id,name,color', 'proposal',
                    'contact.organization'
                ])->findOrFail($request->id);

                Mail::send(new SendDocument($p, $info, $request->type));

                // change status
                $id = settings()->get('contract_status_on_email_sent_id');

                if($id) {
                    $p->status_id = $id;
                    $p->save();
                }
                break;

            case 'invoice':
                $p = Invoice::with([
                    'status:id,name,color', 'proposal',
                    'contract', 'contact.organization'
                ])->findOrFail($request->id);

                Mail::send(new SendDocument($p, $info, $request->type));

                // change status
                $id = settings()->get('invoice_status_on_email_sent_id');

                if($id) {
                    $p->status_id = $id;
                    $p->save();
                }
                break;

            case 'payment':
                $p = Payment::with([
                    'contact.organization', 'deposit:id,name', 'method:id,name',
                    'lines.invoice'
                ])->findOrFail($request->id);

                Mail::send(new SendDocument($p, $info, $request->type));

                // change status
                $id = settings()->get('payment_status_on_email_sent_id');

                if($id) {
                    $p->status_id = $id;
                    $p->save();
                }
                break;

            default:
                # code...
                break;
        }

        return to_json([
            'saved' => true
        ]);
    }

    public function showBase()
    {
        $this->authorize('access', 'settings.email');
        $form = settings()->getMany([
            'email_base_html',
            'email_stylesheet',
            'email_from_address',
            'email_from_name',
            'global_bcc',
            'recurring_export_email_template',
            'recurring_export_email_subject',
            'user_invite_email_subject',
            'user_invite_email_template'
        ]);

    	return to_json([
    		'form' => $form
    	]);
    }

    public function storeBase(Request $request)
    {
        $this->authorize('access', 'settings.email');
        $request->validate([
            'email_stylesheet' => 'required',
            'email_base_html' => 'required',
            'email_from_address' => 'required|email',
            'email_from_name' => 'required',
            'global_bcc' => 'nullable|email',
            'recurring_export_email_template' => 'required',
            'recurring_export_email_subject' => 'required',
            'user_invite_email_subject' => 'required',
            'user_invite_email_template' => 'required'
        ]);

        settings()->setMany(
            $request->only([
                'email_base_html',
                'email_stylesheet',
                'email_from_address',
                'email_from_name',
                'global_bcc',
                'recurring_export_email_template',
                'recurring_export_email_subject',
                'user_invite_email_subject',
                'user_invite_email_template'
            ])
        );


        return to_json([
            'saved' => true
        ]);
    }
}
