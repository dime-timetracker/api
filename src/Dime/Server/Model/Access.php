<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;
/**
 * AccessToken
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class Access extends Model
{
    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }
}
