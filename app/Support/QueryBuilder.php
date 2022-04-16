<?php

namespace App\Support;
use Carbon\Carbon;

class QueryBuilder {

    protected $defaultFilterMatch = 'and';

    public function apply($query, $data)
    {
        return $query->when(isset($data['f']), function($query) use ($data) {
            foreach($data['f'] as $filter) {
                $this->applyFilter($filter, $query, $data);
            }
        });
    }

    protected function applyFilter($filter, $query, $data)
    {
        $filter['match'] = $data['filter_match'] ?: $this->defaultFilterMatch;

        if (strpos($filter['column'], '.') !== false) {
            // nested column
            list($relation, $relatedColumn) = explode('.', $filter['column']);
            $filter['column'] = $relatedColumn;
            $filter['match'] = 'and';

            $query->whereHas($relation, function($q) use ($filter) {
                $this->{camel_case($filter['operator'])}($filter, $q);
            });
        } else {
            // normal column
            $this->{camel_case($filter['operator'])}($filter, $query);
        }
    }

    // equal_to:q1
    protected function equalTo($filter, $query)
    {
        return $query->where($filter['column'], '=', $filter['query_1'], $filter['match']);
    }

    // not_equal_to:q1
    protected function notEqualTo($filter, $query)
    {
        return $query->where($filter['column'], '<>', $filter['query_1'], $filter['match']);
    }

    // less_than:q1
    protected function lessThan($filter, $query)
    {
        return $query->where($filter['column'], '<', $filter['query_1'], $filter['match']);
    }

    // greater_than:q1
    protected function greaterThan($filter, $query)
    {
        return $query->where($filter['column'], '>', $filter['query_1'], $filter['match']);
    }

    // greater_than_or_equal_to:q1
    protected function greaterThanOrEqualTo($filter, $query)
    {
        return $query->where($filter['column'], '>=', $filter['query_1'], $filter['match']);
    }

    // less_than_or_equal_to:q1
    protected function lessThanOrEqualTo($filter, $query)
    {
        return $query->where($filter['column'], '<=', $filter['query_1'], $filter['match']);
    }

    // between:q1,q2
    protected function between($filter, $query)
    {
        return $query->whereBetween($filter['column'], [
            $filter['query_1'], $filter['query_2']
        ], $filter['match']);
    }

    // not_between:q1,q2
    protected function notBetween($filter, $query)
    {
        return $query->whereNotBetween($filter['column'], [
            $filter['query_1'], $filter['query_2']
        ], $filter['match']);
    }

    // in:q1
    protected function includes($filter, $query)
    {
        return $query->whereIn($filter['column'], explode(',,', $filter['query_1']));
    }

    // not_in:q1
    protected function notIncludes($filter, $query)
    {
        return $query->whereNotIn($filter['column'], explode(',,', $filter['query_1']), $filter['match']);
    }

    // is_empty:q1*
    protected function isEmpty($filter, $query)
    {
        return $query->whereNull($filter['column'], $filter['match']);
    }

    // is_not_empty:q1*
    protected function isNotEmpty($filter, $query)
    {
        return $query->whereNotNull($filter['column'], $filter['match']);
    }

    // contains:q1
    protected function contains($filter, $query)
    {
        return $query->where($filter['column'], 'like', '%'.$filter['query_1'].'%', $filter['match']);
    }

    // starts_with:q1
    protected function startsWith($filter, $query)
    {
        return $query->where($filter['column'], 'like', $filter['query_1'].'%', $filter['match']);
    }

    // ends_with:q1
    protected function endsWith($filter, $query)
    {
        return $query->where($filter['column'], 'like', '%'.$filter['query_1'], $filter['match']);
    }

    protected function toggle($filter, $query)
    {
        return $query->where($filter['column'], $filter['query_1'], $filter['match']);
    }

    // yes:q1
    // protected function yes($filter, $query)
    // {
    //     return $query->where($filter['column'], true, $filter['match']);
    // }

    // // no:q1
    // protected function no($filter, $query)
    // {
    //     return $query->where($filter['column'], false, $filter['match']);
    // }

    // in_the_past:q1,q2
    protected function inThePast($filter, $query)
    {
        $end = now()->endOfDay();

        $begin = now();

        switch ($filter['query_2']) {
            case 'hours':
                $begin->subHours($filter['query_1']);
                break;
            case 'days':
                $begin->subDays($filter['query_1'])->startOfDay();
                break;
            case 'months':
                $begin->subMonths($filter['query_1'])->startOfDay();
                break;
            case 'years':
                $begin->subYears($filter['query_1'])->startOfDay();
                break;
            default:
                $begin->subDays($filter['query_1'])->startOfDay();
                break;
        }

        return $query->whereBetween($filter['column'], [$begin, $end], $filter['match']);
    }

    // // in_the_next:q1,q2
    protected function inTheNext($filter, $query)
    {
        $begin = now()->startOfDay();

        $end = now();

        switch ($filter['query_2']) {
            case 'hours':
                $end->addHours($filter['query_1']);
                break;
            case 'days':
                $end->addDays($filter['query_1'])->endOfDay();
                break;
            case 'months':
                $end->addMonths($filter['query_1'])->endOfDay();
                break;
            case 'years':
                $end->addYears($filter['query_1'])->endOfDay();
                break;
            default:
                $end->addDays($filter['query_1'])->endOfDay();
                break;
        }

        return $query->whereBetween($filter['column'], [$begin, $end], $filter['match']);
    }

    public function inThePeroid($filter, $query)
    {
        $begin = now();
        $end = now();

        switch ($filter['query_1']) {
            case 'today':
                $begin->startOfDay();
                $end->endOfDay();
                break;
            case 'yesterday':
                $begin->subDays(1)->startOfDay();
                $end->subDays(1)->endOfDay();
                break;
            case 'tomorrow':
                $begin->addDays(1)->startOfDay();
                $end->addDays(1)->endOfDay();
                break;
            case 'last_month':
                $begin->subMonths(1)->startOfMonth();
                $end->subMonths(1)->endOfMonth();
                break;
            case 'this_month':
                $begin->startOfMonth();
                $end->endOfMonth();
                break;
            case 'next_month':
                $begin->addMonths(1)->startOfMonth();
                $end->addMonths(1)->endOfMonth();
                break;
            case 'last_year':
                $begin->subYears(1)->startOfYear();
                $end->subYears(1)->endOfYear();
                break;
            case 'this_year':
                $begin->startOfYear();
                $end->endOfYear();
                break;
            case 'next_year':
                $begin->addYears(1)->startOfYear();
                $end->addYears(1)->endOfYear();
                break;
            default:
                break;
        }

        return $query->whereBetween($filter['column'], [$begin, $end], $filter['match']);
    }

    public function betweenDate($filter, $query)
    {
        $begin = (new Carbon($filter['query_1']))->startOfDay();
        $end = (new Carbon($filter['query_2']))->endOfDay();
        return $query->whereBetween($filter['column'], [$begin, $end], $filter['match']);
    }

    public function equalToDate($filter, $query)
    {
        $begin = (new Carbon($filter['query_1']))->startOfDay();
        $end = (new Carbon($filter['query_1']))->endOfDay();
        return $query->whereBetween($filter['column'], [$begin, $end], $filter['match']);
    }

    public function over($filter, $query)
    {
        $date = now();

        switch ($filter['query_2']) {
            case 'hours':
                $date->subHours($filter['query_1']);
                break;
            case 'days':
                $date->subDays($filter['query_1'])->startOfDay();
                break;
            case 'months':
                $date->subMonths($filter['query_1'])->startOfDay();
                break;
            case 'years':
                $date->subYears($filter['query_1'])->startOfDay();
                break;
            default:
                $date->subDays($filter['query_1'])->startOfDay();
                break;
        }

        return $query->where($filter['column'], '<', $date, $filter['match']);
    }
}
