<?php

namespace Dime\Server\Model;

class Timeslice extends Base
{
    protected $fillable = ['duration', 'startedAt', 'stoppedAt'];
    protected $guarded = ['id', 'user_id'];
    protected $hidden = ['activity_id', 'user_id'];
    protected $touches = ['activity'];

    public function activity()
    {
        return $this->belongsTo('Dime\Server\Model\Activity');
    }

    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }

    public function tags()
    {
        return $this->belongsToMany('Dime\Server\Model\Tag', 'timeslice_tags');
    }

    public function setStartedAtAttribute($value)
    {
        $this->attributes['started_at'] = $value;
        $this->updateDuration();
    }

    public function setStoppedAtAttribute($value)
    {
        $this->attributes['stopped_at'] = $value;
        $this->updateDuration();
    }

    public function updateDuration()
    {
        if (false === is_null($this->stoppedAt) && false === is_null($this->startedAt)) {
            $this->duration = strtotime($this->stoppedAt) - strtotime($this->startedAt);
        } else {
            $this->duration = null;
        }
    }

}
