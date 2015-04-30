<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Base
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
abstract class Base extends Model
{

    use \Eloquence\Database\Traits\CamelCaseModel;

    public function scopeOrdered($query)
    {
        return $query;
    }

    public function scopeFiltered($query, $filter)
    {
        return $query;
    }

}
