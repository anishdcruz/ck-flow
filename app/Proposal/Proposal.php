<?php

namespace App\Proposal;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Contact\Contact;
use App\Opportunity\Opportunity;
use App\Template\Template;

class Proposal extends Model
{
	use Filterable;

    protected $table = 'proposals';

    protected $fillable = [];

    protected $sortable = [
    	'id', 'number', 'issue_date', 'expiry_date', 'created_at'
    ];

    protected $searchable = [
        'number'
    ];

    protected $allowedFilters = [
    	'id', 'created_at', 'number', 'issue_date', 'expiry_date',
    	'status.name',
        'contact.id', 'contact.number', 'contact.name', 'contact.created_at',
        'template.name', 'opportunity.number', 'opportunity.created_at'
    	// todo: more relations
    ];

    protected $hidden = ['custom_values', 'custom_values_2'];

    public function contact()
    {
    	return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function opportunity()
    {
    	return $this->belongsTo(Opportunity::class, 'opportunity_id', 'id');
    }

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id', 'id');
    }

    public function status()
    {
    	return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function exportable()
    {
        return [
            'id', 'number', 'issue_date', 'expiry_date', 'created_at',
            'status.name', 'contact.name',
        ];
    }

    public static function templateVariables($key, $show = true)
    {
        $base = [
            'id', 'number', 'issue_date', 'expiry_date'
        ];

        $b = collect($base)->map(function($item) use ($key) {
            if(is_null($key)) {
                return $item;
            }
            return $key.'.'.$item;
        });

        if($show) {

            $cf = custom_fields_names('proposals');
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
            'id', 'number', 'issue_date', 'expiry_date'
        ];

        $a = collect(array_merge($ab, custom_fields_names('proposals')))
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
