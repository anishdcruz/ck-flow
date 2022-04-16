<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Contact\Contact;
use App\Proposal\Proposal;
use App\Template\Template;

class Contract extends Model
{
	use Filterable;

    protected $table = 'contracts';

    protected $fillable = [
        'title',
        'contact_id',
        'template_id',
        'proposal_id',
        'type_id',
        'start_date',
        'expiry_date',
        'value',
        'auto_renewal',
        'no_of_months'
    ];

    protected $sortable = [
    	'id', 'number', 'created_at', 'start_date', 'expiry_date', 'value'
    ];

    protected $searchable = [
    	'number'
    ];

    protected $allowedFilters = [
    	'id', 'number', 'created_at', 'start_date', 'expiry_date',
    	'auto_renewal', 'period_count', 'period_type', 'title',
    	'value', 'status_id',
        'contact.id', 'contact.number', 'contact.name', 'contact.created_at',
    	'status.name', 'type.name', 'proposal.number', 'proposal.created_at'
    ];

    protected $hidden = [
        'custom_values', 'custom_values_2'
    ];

    public function contact()
    {
    	return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function proposal()
    {
    	return $this->belongsTo(Proposal::class, 'proposal_id', 'id');
    }

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id', 'id');
    }

    public function status()
    {
    	return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function type()
    {
    	return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function exportable()
    {
        return [
            'id', 'number', 'title', 'start_date', 'expiry_date',
            'auto_renewal', 'period_count', 'period_type',
            'value',
        ];
    }

    public static function templateVariables($key, $show = true)
    {
        $base = [
            'id', 'number', 'title', 'start_date', 'expiry_date',
        'auto_renewal', 'period_count', 'period_type',
        'value',
        ];

        $b = collect($base)->map(function($item) use ($key) {
            if(is_null($key)) {
                return $item;
            }
            return $key.'.'.$item;
        });

        if($show) {

            $cf = custom_fields_names('contracts');
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
            'id', 'number', 'title', 'start_date', 'expiry_date',
        'auto_renewal', 'period_count', 'period_type',
        'value',
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
