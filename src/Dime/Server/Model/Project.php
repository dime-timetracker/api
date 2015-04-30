<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use \Eloquence\Database\Traits\CamelCaseModel;

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
