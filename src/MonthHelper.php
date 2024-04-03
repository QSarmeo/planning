<?php

declare(strict_types=1);

namespace App;

class MonthHelper
{
    public function __construct(
        public int $month,
        public int $year
    ) {
    }

    public function getNumberOfDays(): int
    {
        return cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
    }

    /**
     * @return string[] 1-indexed days of month that are a sunday (e.g. 3, 10, 17, 24)
     */
    public function getSundays(): array
    {
        $sundays = [];
        $period = new \DatePeriod(
            new \DateTime("first sunday of $this->year-$this->month"),
            \DateInterval::createFromDateString('next sunday'),
            new \DateTime("last day of $this->year-$this->month"),
            \DatePeriod::INCLUDE_END_DATE
        );

        foreach ($period as $sundayOccurence) {
            $sundays[] = $sundayOccurence->format('d');
        }
        return $sundays;
    }

    /**
     * @return array{}
     */
    public function getHolidays(): array
    {
        return [];
    }
}
