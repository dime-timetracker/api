<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use \Eloquence\Database\Traits\CamelCaseModel;

    protected $fillable = ['name', 'alias', 'enabled', 'rate'];
    protected $guarded = ['id', 'user_id'];
    protected $hidden = ['user_id'];

    public function projects()
    {
        return $this->hasMany('Dime\Server\Model\Project');
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
