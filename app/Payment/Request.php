<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Invoice\Invoice;
use App\Support\Filterable;
use App\Contact\Contact;

class Request extends Model
{
	use Filterable;

    protected $table = 'payment_requests';

    protected $sortable = [
    	'id', 'number', 'email', 'expiry_at',
    	'payment_received_at', 'created_at'
    ];

    protected $searchable = ['number'];

    protected $allowedFilters = [
    	'id', 'number', 'email', 'uuid', 'expiry_at', 'payment_received_at',
    	'created_at', 'contact.id', 'contact.number', 'contact.name',
    	'invoice.number', 'invoice.created_at'
    ];

    protected $appends = ['payment_url'];

    public function invoice()
    {
    	return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function exportable()
    {
        return [
        	'id', 'number', 'email', 'uuid', 'expiry_at', 'payment_received_at',
    		'created_at', 'contact.id', 'contact.number', 'contact.name',
    		'invoice.number', 'invoice.due_date', 'invoice.created_at'
        ];
    }

    public function getPaymentUrlAttribute()
    {
        return url('payment-requests/'.$this->attributes['uuid']);
    }

    public static function emailVariables()
    {
        // base
        $ab = [
            'id', 'number', 'email', 'payment_url', 'expiry_at'
        ];

        $a = collect(array_merge($ab, custom_fields_names('payments')))
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
