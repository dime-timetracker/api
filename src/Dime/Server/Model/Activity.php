<?php

namespace Dime\Server\Model;

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

    public function scopeFiltered($query, $filter)
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

        $filter = split(';', $filter);
        foreach ($filter as $value) {
            preg_match('/^([+-])?([@\/:#])?(.*)$/', $value, $match);
            switch ($match[2]) {
                case '@':
                    if ($match[1] == '-') {
                        $customers['out'][] = $match[3];
                    } else {
                        $customers['in'][] = $match[3];
                    }
                    break;
                case '/':
                    if ($match[1] == '-') {
                        $projects['out'][] = $match[3];
                    } else {
                        $projects['in'][] = $match[3];
                    }
                    break;
                case ':':
                    if ($match[1] == '-') {
                        $services['out'][] = $match[3];
                    } else {
                        $services['in'][] = $match[3];
                    }
                    break;
                case '#':
                    if ($match[1] == '-') {
                        $tags['out'][] = $match[3];
                    } else {
                        $tags['in'][] = $match[3];
                    }
                    break;
            }
        }
        
        $this->filterRelation($query, 'customer', $customers);
        $this->filterRelation($query, 'project', $projects);
        $this->filterRelation($query, 'service', $services);
        $this->filterRelation($query, 'tags', $tags);

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
                $q->whereIn('alias', $values);
            } else {
                $q->whereNotIn('alias', $values);
            }
        });
    }

}
