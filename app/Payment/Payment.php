<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Contact\Contact;
use App\Invoice\Invoice;

class Payment extends Model
{
	use Filterable;

    protected $table = 'payments';

    protected $fillable = [
        'contact_id',
        'payment_date',
        'method_id',
        'reference',
        'deposit_id',
        'amount_received',
        'bank_fees'
    ];

    protected $sortable = [
        'id', 'created_at', 'payment_date', 'number',
        'amount_received', 'amount_applied', 'bank_fees', 'net_amount'
    ];

    protected $searchable = [
        'number'
    ];

    protected $allowedFilters = [
        'id', 'created_at', 'payment_date', 'number',
        'amount_received', 'amount_applied', 'reference', 'bank_fees', 'net_amount',
        'note', 'contact.id', 'contact.number', 'contact.name', 'contact.created_at',
        'method.name', 'deposit.name'
    ];

    public function method()
    {
    	return $this->belongsTo(Method::class, 'method_id', 'id');
    }

    public function deposit()
    {
    	return $this->belongsTo(Deposit::class, 'deposit_id', 'id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function lines()
    {
        return $this->hasMany(Line::class, 'payment_id', 'id');
    }

    public function exportable()
    {
        return [
            'id', 'payment_date', 'number', 'bank_fees',
            'amount_received', 'amount_applied', 'net_amount', 'reference', 'created_at',
            'note', 'contact.id', 'contact.number', 'contact.name', 'contact.created_at',
            'method.name', 'deposit.name'
        ];
    }

    public static function templateVariables($key, $show = false)
    {
        $base = [
            'id', 'payment_date', 'number',
            'amount_received', 'amount_applied', 'reference', 'bank_fees', 'net_amount',
            'applied_invoices_table',
            'deposit.name', 'method.name'
        ];

        $b = collect($base)->map(function($item) use ($key) {
            if(is_null($key)) {
                return $item;
            }
            return $key.'.'.$item;
        });

        if($show) {

            $cf = custom_fields_names('payments');
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
            'id', 'payment_date', 'number',
            'amount_received', 'amount_applied', 'reference', 'bank_fees', 'net_amount',
            'deposit.name', 'method.name'
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
