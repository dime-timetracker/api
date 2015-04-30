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
        $filter = split(';', $filter);
        
        $customers = array();
        $projects = array();
        $services = array();
        $tags = array();

        foreach ($filter as $value) {
            preg_match('/^([+-])?([@\/:#])?(.*)$/', $value, $match);
            switch ($match[2]) {
                case '@':
                    $customers[] = $match[3];
                    break;
                case '/':
                    $projects[] = $match[3];
                    break;
                case ':':
                    $services[] = $match[3];
                    break;
                case '#':
                    $tags[] = $match[3];
                    break;
            }
        }

        if (!empty($customers)) {
            $query = $query->whereHas('customer', function($q) use ($customers) {
                $q->whereIn('alias', $customers);
            });
        }

        if (!empty($projects)) {
            $query = $query->whereHas('project', function($q) use ($projects) {
                $q->whereIn('alias', $projects);
            });
        }

        if (!empty($services)) {
            $query = $query->whereHas('service', function($q) use ($services) {
                $q->whereIn('alias', $services);
            });
        }

        return $query;
    }

}
