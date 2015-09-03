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
        $relationParser = new RelationParser();
        $filters = $relationParser->run($filterString);
        $filterString = $relationParser->clean($filterString);
        foreach ($filters as $relation=>$filter) {
            if (!is_array($filter)) {
                $filter = ['in' => [$filter]];
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
                $query->where('description', 'not like', array_map(function ($string) { return '%'.$string.'%'; }, $filter['out']));
            }
        }

        return $query;
    }

    public function filterRelation($query, $name, array $values) {
        if (!empty($values['in'])) {
            $query = $this->inRelation($query, $name, $values['in'], true);
        } else if (!empty($values['out'])) {
            $query = $this->inRelation($query, $name, $values['out'], false);
        }
        return $query;
    }

    public function inRelation($query, $name, array $values, $isRelated = true) {
        return $query->whereHas($name, function($q) use ($values, $isRelated) {
            if ($isRelated) {
                $q->whereIn('alias', $values);
            } else {
                $q->whereNotIn('alias', $values);
            }
        });
    }
}
