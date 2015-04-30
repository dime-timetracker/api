<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use \Eloquence\Database\Traits\CamelCaseModel;
    
    protected $fillable = ['name', 'namespace', 'value'];
    protected $guarded = ['id', 'user_id'];
    protected $hidden = ['user_id'];

    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }

    public function scopeOrdered($query)
    {
        return $query;
    }

}
