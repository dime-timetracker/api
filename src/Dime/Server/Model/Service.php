<?php

namespace Dime\Server\Model;

class Service extends Base
{

    protected $fillable = ['name', 'description', 'alias', 'rate', 'enabled'];
    protected $guarded = ['id', 'user_id'];
    protected $hidden = ['user_id'];

    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }

}
