<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    protected $fillable = [
        'description', 'rate', 'rate_reference', 'customer_id', 'project_id', 'service_id'
    ];
    protected $guarded = ['id', 'user_id'];

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

}
