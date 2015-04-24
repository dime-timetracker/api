<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    public function customer()
    {
        return $this->belongsTo('Dime\Server\Model\Customer');
    }

    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }

    public function deepload()
    {
        $this->customer = Customer::find($this->customer_id);
    }

    public function getValidator($userId = null)
    {
        $userId = is_null($userId) ? \Auth::user()->id : $userId;
        return \Validator::make($this->toArray(), [
                    'name' => 'required',
                    'alias' => 'required',
                    'enabled' => 'boolean',
                    'customerId' => 'required|exists:customers,id,user_id,' . $userId,
                    'userId' => 'required|between:' . $userId . ',' . $userId,
        ]);
    }

}
