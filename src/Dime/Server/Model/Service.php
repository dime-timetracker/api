<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }

}
