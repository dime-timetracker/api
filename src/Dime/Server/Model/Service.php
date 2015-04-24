<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }

    public function getValidator($userId = null)
    {
        $userId = is_null($userId) ? \Auth::user()->id : $userId;
        return \Validator::make($this->toArray(), [
                    'name' => 'required',
                    'alias' => 'required',
                    'enabled' => 'boolean',
                    'userId' => 'required|between:' . $userId . ',' . $userId,
        ]);
    }

}
