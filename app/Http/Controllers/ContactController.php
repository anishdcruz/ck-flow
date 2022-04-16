<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact\Contact;
use App\Invoice\Invoice;
use App\Services\ExportCSV;
use DB;
use App\Organization\Organization;

class ContactController extends Controller
{
	public function typeahead()
	{
        $collection = Contact::typeahead([
            'number', 'name'
        ], [
            'id', 'number', 'name'
        ]);

        if(request()->has('invoices')) {

            $st = json_decode(settings()->get('receive_payment_on_status_ids'), true);

            $collection = $collection->map(function($item) use ($st) {
                $item->invoices = Invoice::select(['amount_paid', 'grand_total', 'due_date', 'issue_date', 'number', 'id'])
                    ->where('contact_id', $item->id)
                    ->whereIn('status_id', $st)
                    ->get()
                    ->map(function($i) {
                        $i->invoice_id = $i->id;
                        $i->amount_applied = 0;
                        $i->balance = $i->grand_total - $i->amount_paid;
                        return $i;
                    });
                return $item;
            });
        }

	    return to_json([
	        'results' => $collection
	    ]);
	}

	public function search()
	{
        $collection = Contact::search();

	    return to_json([
	        'collection' => $collection
	    ]);
	}

    public function export()
    {
        $this->authorize('access', 'contacts.export');
        return (new ExportCSV(Contact::with('organization'), 'contacts'))
            ->download();
    }

    public function index()
    {
        $this->authorize('access', 'contacts.index');

        $collection = Contact::with(['organization:id,name'])
            ->when(request('organization_id'), function($query) {
                return $query->where('organization_id', request('organization_id'));
            })->filter();

        return to_json([
            'collection' => $collection
        ]);
    }

    public function create()
    {
        $this->authorize('access', 'contacts.create');
        $contact = [
            'name' => '',
            'organization' => null,
            'organization_id' => '',
            'email' => '',
            'title' => '',
            'department' => '',
            'mobile' => '',
            'work' => '',
            'fax' => '',
            'website' => '',
            'primary_address' => '',
            'other_address' => '',
            'number' => __('lang.auto_generated'),
            'custom_fields' => custom_fields('contacts', "[]")
        ];

        if(request()->has('organization_id')) {
            $org = Organization::findOrFail(request()->organization_id);
            $contact['organization_id'] = $org->id;
            $contact['organization'] = [
                'name' => $org->name,
                'id' => $org->id
            ];
        }

        return to_json([
            'form' => $contact
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'contacts.create');
        $request->validate([
            'name' => 'required|string',
            'organization_id' => 'nullable|integer|exists:organizations,id',
            'email' => 'required|email|string|unique:contacts,email,',
            'title' => 'nullable|string',
            'department' => 'nullable|string',
            'mobile' => 'nullable|string',
            'work' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'primary_address' => 'required|string',
            'other_address' => 'nullable|string',
            'custom_fields' => 'array'
        ]);

        $contact = new Contact;
        $contact->fill($request->all());
        $contact->custom_values = json_encode(custom_values($request->custom_fields));


        $contact = DB::transaction(function() use ($contact) {
            $c = counter('contact');
            $contact->number = $c->number;
            $contact->save();
            $c->increment('value');

            return $contact;
        });

        return to_json([
            'saved' => true,
            'id' => $contact->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'contacts.show');
        $contact = Contact::with([
        	'organization:id,name'
        	])
        	->findOrFail($id);

        $contact->custom_fields = custom_fields_preview('contacts', $contact->custom_values);

        return to_json([
            'model' => $contact
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'contacts.update');
        $contact = Contact::with([
            'organization:id,name'
            ])
            ->findOrFail($id);

        $contact->custom_fields = custom_fields('contacts', $contact->custom_values);

        return to_json([
            'form' => $contact
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'contacts.update');
        $request->validate([
            'name' => 'required|string',
            'organization_id' => 'nullable|integer|exists:organizations,id',
            'email' => 'required|email|string|unique:contacts,email,'.$id.',id',
            'title' => 'nullable|string',
            'department' => 'nullable|string',
            'mobile' => 'nullable|string',
            'work' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'primary_address' => 'required|string',
            'other_address' => 'nullable|string',
            'custom_fields' => 'array'
        ]);

        $contact = Contact::findOrFail($id);
        $contact->fill($request->all());
        $contact->custom_values = json_encode(custom_values($request->custom_fields));

        $contact->save();

        return to_json([
            'saved' => true,
            'id' => $contact->id
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'contacts.delete');
        $model = Contact::findOrFail($id);

        if(DB::table('opportunities')->where('contact_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('proposals')->where('contact_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('contracts')->where('contact_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('projects')->where('contact_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('invoices')->where('contact_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('payments')->where('contact_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('payment_requests')->where('contact_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
