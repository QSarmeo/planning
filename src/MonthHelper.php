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
     * Only supports MAY 2024
     * Warning : 26/05/2024 is set as a holiday to have a sunday/holiday combo
     *      (And it's Mother's Day, so it should be a holiday !)
     *
     * @return string[] 1-indexed days of month that are a holiday (e.g. 3, 10, 17, 24)
     */
    public function getHolidays(): array
    {
        return match(true) {
            ($this->month === 5 && $this->year === 2024) => ['01', '08', '09', '20', '26'],
            default => [],
        };
    }
}
