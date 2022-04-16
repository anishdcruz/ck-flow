<?php

namespace App\Invoice;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Contact\Contact;
use App\Contract\Contract;
use App\Template\Template;
use App\Proposal\Proposal;

class Invoice extends Model
{
	use Filterable;

    protected $table = 'invoices';

    protected $fillable = [
    	'contact_id',
    	'template_id',
    	'proposal_id',
    	'contract_id',
    	'issue_date',
    	'due_date',
    	'reference'
    ];

    protected $sortable = [
        'id', 'number', 'issue_date', 'due_date',
        'sub_total', 'grand_total',
        'amount_paid',
        'created_at'
    ];

    protected $searchable = ['number'];

    protected $allowedFilters = [
        'id', 'number', 'issue_date', 'due_date',
        'sub_total', 'grand_total',
        'amount_paid', 'reference', 'created_at',
        'contact.id', 'contact.number', 'contact.name', 'contact.created_at',
        'proposal.number', 'proposal.created_at',
        'contract.number', 'contract.created_at', 'status.name'
    ];

    protected $hidden = ['custom_values', 'custom_values_2'];

    public function getBalanceAttribute()
    {
        return $this->attributes['grand_total'] - $this->attributes['amount_paid'];
    }

    public function lines()
    {
    	return $this->hasMany(Line::class, 'invoice_id', 'id');
    }

    public function status()
    {
    	return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function contact()
    {
    	return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function template()
    {
    	return $this->belongsTo(Template::class, 'template_id', 'id');
    }

    public function proposal()
    {
    	return $this->belongsTo(Proposal::class, 'proposal_id', 'id');
    }

    public function contract()
    {
    	return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function exportable()
    {
        return [
            'id', 'number', 'issue_date', 'due_date',
            'sub_total', 'grand_total',
            'amount_paid', 'reference', 'created_at',
            'contact.id', 'contact.number', 'contact.name', 'contact.created_at',
            'proposal.number', 'proposal.created_at',
            'contract.number', 'contract.created_at'
        ];
    }

    public static function templateVariables($key, $show = true)
    {
        $base = [
            'id', 'number', 'issue_date', 'due_date',
            'sub_total', 'grand_total',
            'amount_paid', 'reference',
        ];

        $b = collect($base)->map(function($item) use ($key) {
            if(is_null($key)) {
                return $item;
            }
            return $key.'.'.$item;
        });

        if($show) {

            $cf = custom_fields_names('invoices');
            $c = collect($cf)->map(function($item) use ($key) {
                if(is_null($key)) {
                    return $item;
                }
                return $key.'.'.$item;
            });

            $f = $b->merge($c);
            return $f->toArray();
        }


        return $b->toArray();
    }

    public static function emailVariables()
    {
        // base
        $ab = [
            'id', 'number', 'issue_date', 'due_date',
            'sub_total', 'grand_total',
            'amount_paid', 'reference',
        ];

        $a = collect(array_merge($ab, custom_fields_names('contracts')))
            ->map(function($item) {
            return $item;
        });

        // contacts
        $cb = [
            'number', 'name', 'title',
            'department', 'mobile', 'phone',
            'fax', 'email','website',
            'primary_address', 'other_address',
        ];

        $c = collect($cb)
            ->map(function($item) {
            return 'contact.'.$item;
        });

        // organizations
        $cd = [
            'id', 'number',
            'name', 'title', 'department',
            'phone', 'fax', 'email','website',
            'primary_address', 'other_address',
        ];

        $d = collect($cd)
            ->map(function($item) {
            return 'contact.organization.'.$item;
        });

        return $c->merge($a)->merge($d)->toArray();
    }
}
