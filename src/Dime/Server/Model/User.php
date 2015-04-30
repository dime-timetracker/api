<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use \Eloquence\Database\Traits\CamelCaseModel;
    
    protected $fillable = ['username', 'email', 'firstname', 'lastname', 'enabled'];
    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public function activities()
    {
        return $this->hasMany('Dime\Server\Model\Activity');
    }

    public function customers()
    {
        return $this->hasMany('Dime\Server\Model\Customer');
    }

    public function projects()
    {
        return $this->hasMany('Dime\Server\Model\Project');
    }

    public function services()
    {
        return $this->hasMany('Dime\Server\Model\Service');
    }

    public function tags()
    {
        return $this->hasMany('Dime\Server\Model\Tags');
    }

    public function timeslices()
    {
        return $this->hasMany('Dime\Server\Model\Timeslice');
    }

}
