<?php

namespace Dime\Server\Model;

class Project extends Base
{

    protected $fillable = [
        'name', 'description', 'alias', 'enabled',
        'rate', 'budgetPrice', 'budgetTime', 'isBudgetFixed'
    ];
    protected $guarded = ['id', 'user_id'];
    protected $hidden = ['customer_id', 'user_id'];

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

}
