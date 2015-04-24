<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Timeslice extends Model
{

    protected $fillable = ['activity_id', 'duration', 'started_at', 'stopped_at'];
    protected $guarded = ['id', 'user_id'];

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function tags()
    {
        return $this->belongsToMany('Dime\Server\Model\Tags', 'timeslice_tags');
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
