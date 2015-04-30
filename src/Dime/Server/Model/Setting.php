<?php

namespace Dime\Server\Model;

class Setting extends Base
{
    protected $fillable = ['name', 'namespace', 'value'];
    protected $guarded = ['id', 'user_id'];
    protected $hidden = ['user_id'];

    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }

}
