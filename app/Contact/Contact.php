<?php

namespace App\Contact;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Organization\Organization;
use App\Note;
use App\PhoneCall;
use App\Activity\Activity;
use App\Reminder;
use App\Invoice\Invoice;

class Contact extends Model
{
	use Filterable;

    protected $table = 'contacts';

    protected $fillable = [
        'name', 'title', 'department', 'organization_id',
        'mobile', 'phone', 'fax', 'email','website',
        'primary_address', 'other_address',
    ];

    protected $sortable = [
    	'id', 'number', 'name', 'total_revenue',
    	'created_at'
    ];

    protected $searchable = [
        'number', 'name', 'email', 'fax', 'phone', 'website', 'mobile'
    ];

    protected $allowedFilters = [
    	'id', 'number',
    	'name', 'title', 'department',
    	'mobile', 'phone', 'fax', 'email','website',
    	'primary_address', 'other_address',
        'total_revenue', 'amount_receivable',
    	'last_contact', 'last_project', 'last_invoice',
    	'last_payment', 'last_proposal', 'created_at',
    	// organization
        'organization.id', 'organization.number', 'organization.name',
        'organization.created_at'
    ];

    protected $hidden = ['custom_values'];

    public function organization()
    {
    	return $this->belongsTo(Organization::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'callable');
    }

    public function reminders()
    {
        return $this->morphMany(Reminder::class, 'reminderable');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function exportable()
    {
        return [
            'id', 'number',
            'name', 'title', 'department',
            'mobile', 'phone', 'fax', 'email','website',
            'primary_address', 'other_address',
            'total_revenue',
            'last_contact', 'last_project', 'last_invoice',
            'last_payment', 'last_proposal', 'created_at',
            'organization.id', 'organization.number', 'organization.name',
            'organization.total_revenue',
            'organization.created_at'
        ];
    }

    public static function templateVariables($key = 'contacts', $show = true)
    {
        $base = [
            'id', 'number',
            'name', 'title', 'department',
            'mobile', 'phone', 'fax', 'email','website',
            'primary_address', 'other_address',
        ];



        $b = collect($base)->map(function($item) use ($key) {
            if(is_null($key)) {
                return $item;
            }
            return $key.'.'.$item;
        });

        if($show) {

            $cf = custom_fields_names('contacts');
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

    public function customFields($key)
    {
        return custom_fields_to_array(
            'contacts',
            $key,
            $this->attributes['custom_values']
        );
    }
}
