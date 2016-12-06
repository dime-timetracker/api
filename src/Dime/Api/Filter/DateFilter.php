<?php

namespace Dime\Api\Filter;

use DateTime;
use Dime\Api\Scope\DateScope;
use Dime\Server\Filter\FilterInterface;

class DateFilter implements FilterInterface
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

        $start = $this->parseDate($dates, 0);
        if ($start != null) {
            $start = $start->setTime(0, 0, 0);
        }

        $end = $this->parseDate($dates, 1);
        if ($end != null) {
            $end = $end->setTime(23, 59, 59);
        }

        return new DateScope($start ?: null, $end ?: null);
    }

    /**
     * Parse date
     * @param array $dates
     * @param int $position
     * @return DateTime
     */
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
