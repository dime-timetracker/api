<?php

namespace Dime\Server\Filter;

use DateTime;
use Dime\Server\Scope\Date as DateScope;

class Date implements FilterInterface
{

    const NAME = 'date';
    const FORMAT = 'Y-m-d';

    private $map;

    public function __construct(array $map = ['start' => 'updated_at', 'end' => 'updated_at'])
    {
        $this->map = $map;
    }

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

        return new DateScope($start ?: null, $end ?: null, $this->map);
    }

    private function parseDate(array $dates, $position)
    {
        if (!isset($dates[$position])) {
            return null;
        }
        
        $dates[$position] = trim($dates[$position]);

        if (empty($dates)) {
            return null;
        }
        
        return DateTime::createFromFormat(self::FORMAT, $dates[$position]);
    }

}
