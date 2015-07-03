<?php

namespace Dime\Server\Model;

use Moment\Moment;
use Dime\Parser\ActivityRelationParser as RelationParser;
use Dime\Parser\ActivityDescriptionParser as DescriptionParser;

class Activity extends Base
{

    protected $fillable = [ 'description', 'rate', 'rateReference'];
    protected $guarded = ['id', 'user_id'];
    protected $hidden = ['customer_id', 'project_id', 'service_id', 'user_id'];

    public function customer()
    {
        return $this->belongsTo('Dime\Server\Model\Customer');
    }

    public function project()
    {
        return $this->belongsTo('Dime\Server\Model\Project');
    }

    public function service()
    {
        return $this->belongsTo('Dime\Server\Model\Service');
    }

    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }

    public function timeslices()
    {
        return $this->hasMany('Dime\Server\Model\Timeslice');
    }

    public function tags()
    {
        return $this->belongsToMany('Dime\Server\Model\Tag', 'activity_tags');
    }

    public function scopeOrdered($query)
    {
        return $query->latest('updated_at');
    }

    public function scopeFiltered($query, $filterString)
    {
        $customers = array(
            'in' => array(),
            'out' => array()
        );
        $projects = array(
            'in' => array(),
            'out' => array()
        );
        $services = array(
            'in' => array(),
            'out' => array()
        );
        $tags = array(
            'in' => array(),
            'out' => array()
        );

        $now = new Moment();
        $dateFilters = [
            'today' => [
                'start' => $now->cloning()->startOf('day')
            ],
            'yesterday' => [
                'start' => $now->cloning()->subtractDays(1)->startOf('day'),
                'stop' => $now->cloning()->subtractDays(1)->endOf('day')
            ],
            'current week' => [
                'start' => $now->cloning()->startOf('week')
            ],
            'last week' => [
                'start' => $now->cloning()->subtractWeeks(1)->startOf('week'),
                'stop' => $now->cloning()->subtractWeeks(1)->endOf('week'),
            ],
            'last 4 weeks' => [
                'start' => $now->cloning()->subtractWeeks(4)->startOf('day')
            ],
            'current month' => [
                'start' => $now->cloning()->startOf('month')
            ],
            'last month' => [
                'start' => $now->cloning()->subtractMonths(1)->startOf('month'),
                'stop' => $now->cloning()->subtractMonths(1)->endOf('month')
            ]
        ];

        $relationParser = new RelationParser();
        $filters = $relationParser->run($filterString);
        $filterString = $relationParser->clean($filterString);
        foreach ($filters as $relation=>$filter) {
            if (!array_key_exists('in', $filter) && !array_key_exists('out', $filter)) {
                $filter = ['in' => $filter];
            }
            $query = $this->filterRelation($query, $relation, $filter);
        }
        
        /* TODO: apply timerange filter
        $timerangeParser = new TimerangeParser()
        $filters = $timerangeParser->run($filterString);
        $filterString = $timerangeParser->clean($filterString);
        */

        /* TODO: apply duration filter
        $durationParser = new DurationParser()
        $filters = $durationParser->run($filterString);
        $filterString = $durationParser->clean($filterString);
        */

        $descriptionParser = new DescriptionParser();
        $filters = $descriptionParser->run($filterString);
        $filterString = $descriptionParser->clean($filterString);
        if (isset($filters['description'])) {
            $filter = $filters['description'];
            if (!is_array($filter)) {
                $filter = ['in' => [$filter], 'out' => []];
            }
            if (!empty($filter['in'])) {
                $query->where('description', 'like', array_map(function ($string) { return '%'.$string.'%'; }, $filter['in']));
            }
            if (!empty($filter['out'])) {
                $query->where('description', 'like', array_map(function ($string) { return '%'.$string.'%'; }, $filter['out']));
            }
        }

        return $query;
    }

    public function filterRelation($query, $name, array $values) {
        if (!empty($values['in'])) {
            $query = $this->inRelation($query, $name, $values['in']);
        } else if (!empty($values['out'])) {
            $query = $this->inRelation($query, $name, $values['out'], true);
        }
        return $query;
    }

    public function inRelation($query, $name, array $values, $not = false) {
        return $query->whereHas($name, function($q) use ($values, $not) {
            if ($not) {
                $q->whereNotIn('alias', $values);
            } else {
                $q->whereIn('alias', $values);
            }
        });
    }

}
