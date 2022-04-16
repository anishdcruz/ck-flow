<?php

namespace App\Support;

use App\Support\QueryBuilder;
use Illuminate\Validation\ValidationException;
use DB;
use Carbon\Carbon;

trait Filterable {

    protected $defaultSortColumn = 'created_at';
    protected $defaultSortDirection = 'desc';

    public function scopeTypeahead($query, $columns, $required)
    {
        return $query->when(request('query'), function($query) use ($columns) {
                foreach($columns as $column) {
                    $query->orWhere($column, 'like', '%'.request('query').'%');
                }
            })
            ->limit(10)
            ->get($required);
    }

    public function scopeSearch($query)
    {
        $this->validate(request()->all(), [
            'column' => 'required|in:'.$this->searchableColumns()
        ]);

        return $query->select('id',request('column'), DB::Raw(request('column'). ' as value'))
            ->orderBy(request('column'))
            ->whereNotNull(request('column'))
            ->when(request('query'), function($q) {
                $q->where(request('column'), 'like', '%'.request('query').'%');
            })
            ->limit(10)
            ->get();
    }

    public function scopeExportable($query)
    {
        return $this->exportable();
    }

    public function scopeExport($query, $params)
    {
        $query = $this->process($params, $query);

        $response = $query->orderBy(
            request('sort_column', $this->defaultSortColumn),
            request('sort_direction', $this->defaultSortDirection)
        )
        ->get();

        return $response;
    }

    public function scopeServerExport($query, $params)
    {
        $query = $this->process($params, $query);

        $response = $query->orderBy(
            $params['sort_column'] ?: $this->defaultSortColumn,
            $params['sort_direction'] ?: $this->defaultSortDirection
        );


        return $response->get();
    }

    public function scopeMetrics($query, $params, $type, $column, $time_peroid)
    {
        $response = $this->process($params, $query);

        // if($type == 'chart') {
        //     $start = now()->subMonths(11)->startOfMonth();
        //     $end = now()->endOfMonth();
        //     $dates = [];

        //     while ($start->lte($end)) {
        //         $dates = array_add($dates, $start->copy()->format('d'), 0);
        //         $start->addDay(1);
        //     }

        //     // $column = $params['group_by'];

        //     $res = $response->groupBy($column)
        //         // ->select(DB::raw('day(created_at) as date'), DB::raw('count(*) as total'))
        //     ->select(DB::raw('count(*) as total'), DB::raw("DATE_FORMAT(".$column.", '%d') value"))
        //         ->pluck('total', 'value');
        //     $all =  ($res->toArray() + $dates);
        //     ksort($all);
        //     return collect(array_values($all))->map(function($item) {
        //         return ['value' => $item];
        //     });
        // }
        // return $response->count();
        if($type == 'chart') {

            if($time_peroid == 'yesterday') {
                $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%H') value");
                $start = now()->subDays(1)->startOfDay();
                $end = now()->subDays(1)->endOfDay();
                $dates = [];

                while ($start->lte($end)) {
                    $dates = array_add($dates, $start->copy()->format('H'), 0);
                    $start->addHour(1);
                }
            } elseif($time_peroid == 'today') {
                $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%H') value");
                $start = now()->startOfDay();
                $end = now()->endOfDay();
                $dates = [];

                while ($start->lte($end)) {
                    $dates = array_add($dates, $start->copy()->format('H'), 0);
                    $start->addHour(1);
                }
            } elseif($time_peroid == 'last_month') {
                $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%d') value");
                $start = now()->subMonths(1)->startOfMonth();
                $end = now()->subMonths(1)->endOfMonth();
                $dates = [];

                while ($start->lte($end)) {
                    $dates = array_add($dates, $start->copy()->format('d'), 0);
                    $start->addDay(1);
                }
            } elseif($time_peroid == 'this_month') {
                $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%d') value");
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
                $dates = [];

                while ($start->lte($end)) {
                    $dates = array_add($dates, $start->copy()->format('d'), 0);
                    $start->addDay(1);
                }
            } elseif($time_peroid == 'last_year') {
                $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%m') value");
                $start = now()->subYears(1)->startOfYear();
                $end = now()->subYears(1)->endOfYear();
                $dates = [];

                while ($start->lte($end)) {
                    $dates = array_add($dates, $start->copy()->format('m'), 0);
                    $start->addMonth(1);
                }
            } elseif($time_peroid == 'this_year') {
                $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%m') value");
                $start = now()->startOfYear();
                $end = now()->endOfYear();
                $dates = [];

                while ($start->lte($end)) {
                    $dates = array_add($dates, $start->copy()->format('m'), 0);
                    $start->addMonth(1);
                }
            }

            $res = $response->groupBy($column)
                ->select(DB::raw('count(*) as total'), $valueFormat)
                ->pluck('total', 'value');

            $all =  $res->toArray() + $dates;

            ksort($all);

            return collect(array_values($all))->map(function($item) {
                return ['value' => $item];
            });
        }
        return $response->count();
    }

    public function scopeFilter($query, $cols = null)
    {
        $response = $this->process(request()->all(), $query);

        if(request()->has('metric_type')) {
            if(request('metric_type') == 'chart') {
                $column = request('group_by');
                if(request('time_peroid') == 'yesterday') {
                    $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%H') value");
                    $start = now()->subDays(1)->startOfDay();
                    $end = now()->subDays(1)->endOfDay();
                    $dates = [];

                    while ($start->lte($end)) {
                        $dates = array_add($dates, $start->copy()->format('H'), 0);
                        $start->addHour(1);
                    }
                } elseif(request('time_peroid') == 'today') {
                    $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%H') value");
                    $start = now()->startOfDay();
                    $end = now()->endOfDay();
                    $dates = [];

                    while ($start->lte($end)) {
                        $dates = array_add($dates, $start->copy()->format('H'), 0);
                        $start->addHour(1);
                    }
                } elseif(request('time_peroid') == 'last_month') {
                    $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%d') value");
                    $start = now()->subMonths(1)->startOfMonth();
                    $end = now()->subMonths(1)->endOfMonth();
                    $dates = [];

                    while ($start->lte($end)) {
                        $dates = array_add($dates, $start->copy()->format('d'), 0);
                        $start->addDay(1);
                    }
                } elseif(request('time_peroid') == 'this_month') {
                    $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%d') value");
                    $start = now()->startOfMonth();
                    $end = now()->endOfMonth();
                    $dates = [];

                    while ($start->lte($end)) {
                        $dates = array_add($dates, $start->copy()->format('d'), 0);
                        $start->addDay(1);
                    }
                } elseif(request('time_peroid') == 'last_year') {
                    $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%m') value");
                    $start = now()->subYears(1)->startOfYear();
                    $end = now()->subYears(1)->endOfYear();
                    $dates = [];

                    while ($start->lte($end)) {
                        $dates = array_add($dates, $start->copy()->format('m'), 0);
                        $start->addMonth(1);
                    }
                } elseif(request('time_peroid') == 'this_year') {
                    $valueFormat = DB::raw("DATE_FORMAT(".$column.", '%m') value");
                    $start = now()->startOfYear();
                    $end = now()->endOfYear();
                    $dates = [];

                    while ($start->lte($end)) {
                        $dates = array_add($dates, $start->copy()->format('m'), 0);
                        $start->addMonth(1);
                    }
                }

                $res = $response->groupBy($column)
                    ->select(DB::raw('count(*) as total'), $valueFormat)
                    ->pluck('total', 'value');

                $all =  $res->toArray() + $dates;

                ksort($all);

                return collect(array_values($all))->map(function($item) {
                    return ['value' => $item];
                });
            }
            return $response->count();
        }

        $response = $query->orderBy(
            request('sort_column', $this->defaultSortColumn),
            request('sort_direction', $this->defaultSortDirection)
        );

        return $response->paginate(
            request('limit', config('flow.per_page')), $cols
        );
    }

    protected function process($data, $query)
    {
        $this->validate($data, [
            'sort_column' => 'sometimes|required|in:'.$this->sortableColumns(),
            'sort_direction' => 'sometimes|required|in:asc,desc',

            'limit' => 'sometimes|required|integer|min:1',
            'page' => 'sometimes|required|integer|min:1',

            // advanced multi-column filter
            'filter_match' => 'sometimes|required|in:and,or',
            'f' => 'sometimes|required|array',
            'f.*.column' => 'required|in:'.$this->whiteListColumns(),
            'f.*.operator' => 'required_with:f.*.column|in:'.$this->allowedOperators(),
            'f.*.query_1' => 'required_unless:f.*.operator,is_empty,is_not_empty',
            'f.*.query_2' => 'required_if:f.*.operator,between,between_date',
        ]);

        return (new QueryBuilder)->apply($query, $data);
    }

    protected function validate($data, $rules)
    {
        $v = validator()->make($data, $rules);

        if($v->fails()) {
            // if (env('APP_ENV') == 'local') {
            //     return dd($v->messages()->all());
            // }

            throw new ValidationException($v);
        }
    }

    protected function whiteListColumns()
    {
        return implode(',', $this->allowedFilters);
    }

    public function sortableColumns()
    {
        return implode(',', $this->sortable);
    }

    public function searchableColumns()
    {
        return implode(',', $this->searchable);
    }

    protected function allowedOperators()
    {
        return implode(',', [
            'equal_to',
            'not_equal_to',
            'less_than',
            'greater_than',
            'less_than_or_equal_to',
            'greater_than_or_equal_to',
            'between',
            'not_between',
            'includes',
            'not_includes',
            'is_empty',
            'is_not_empty',
            'contains',
            'starts_with',
            'ends_with',
            'in_the_past',
            'in_the_next',
            'in_the_peroid',
            'over',
            'between_date',
            'equal_to_date',
            'toggle'
        ]);
    }
}
