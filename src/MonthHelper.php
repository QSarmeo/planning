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
     * @return array{}
     */
    public function getSundays(): array
    {
        return [];
    }

    /**
     * @return array{}
     */
    public function getHolidays(): array
    {
        return [];
    }
}
