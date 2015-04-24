<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    public function activities()
    {
        return $this->belongsToMany('Dime\Server\Model\Activity');
    }

    public function timeslices()
    {
        return $this->belongsTo('Dime\Server\Model\Timeslice');
    }

    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }

}
