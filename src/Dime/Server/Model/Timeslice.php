<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Timeslice extends Model
{

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

    public function getValidator($userId = null)
    {
        $userId = is_null($userId) ? \Auth::user()->id : $userId;
        return \Validator::make($this->toArray(), [
                    'startedAt' => 'required|date',
                    'stoppedAt' => 'date',
                    //'duration'   => 'integer',
                    'userId' => 'required|between:' . $userId . ',' . $userId,
                    'activityId' => 'required|exists:activities,id,user_id,' . $userId,
        ]);
    }

}
