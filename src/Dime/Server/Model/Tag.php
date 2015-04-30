<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use \Eloquence\Database\Traits\CamelCaseModel;

    protected $fillable = ['name', 'enabled'];
    protected $guarded = ['id', 'user_id'];
    protected $hidden = ['user_id'];

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

    public function scopeOrdered($query)
    {
        return $query;
    }

}
