<?php

namespace Dime\Server\Filter;

use Dime\Server\Scope\Date as DateScope;
use DateTime;

class Date implements FilterInterface
{

    const NAME = 'date';
    const FORMAT = 'Y-m-d';

    public function name()
    {
        return self::NAME;
    }

    public function __invoke($value)
    {
        $dates = explode(';', $value);

        // TODO Parsing of [], (], () ...

        $start = $this->parseDate($dates, 0);
        $end = $this->parseDate($dates, 1);

        return new DateScope($start, $end);
    }

    private function parseDate(array $dates, $position)
    {
        if (!isset($dates[$position])) {
            return null;
        }
        
        $date[$position] = trim($date[$position]);

        if (empty($date)) {
            return null;
        }
        
        return DateTime::createFromFormat(self::FORMAT, $dates[$position]);
    }

}
