<?php

declare(strict_types=1);

namespace App;

use Umulmrum\Holiday\Filter\IncludeTimespanFilter;
use Umulmrum\Holiday\Formatter\DateFormatter;
use Umulmrum\Holiday\HolidayCalculator;
use Umulmrum\Holiday\Provider\France\France;

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
     * @return string[] 1-indexed days of month that are a holiday (e.g. 3, 10, 17, 24)
     */
    public function getHolidays(): array
    {
        $firstDay = new \DateTime("first day of $this->year-$this->month");
        $lastDay = new \DateTime("last day of $this->year-$this->month");

        $holidays = (new HolidayCalculator())
            ->calculate(France::class, $this->year)
            ->filter(new IncludeTimespanFilter($firstDay, $lastDay));

        /** @var string[] $formattedHolidayList */
        $formattedHolidayList = (new DateFormatter('d'))->formatList($holidays);

        return $formattedHolidayList;
    }
}
