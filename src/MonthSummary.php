<?php

declare(strict_types=1);

namespace App;

class MonthSummary
{
    public const MAX_WORKDAY_HOURS = 8;

    public function __construct(
        int $month = 05,
        int $year = 2024,
        public int $regularDays = 0,
        public int $regularHours = 0,
        public int $sundayDays = 0,
        public int $sundayHours = 0,
        public int $holidayDays = 0,
        public int $holidayHours = 0,
        public int $awayDays = 0,
    ) {
        $this->awayDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    /**
     * @param array{
     *     date: string,
     *     duration: int
     * }[] $input
     *
     * @return array{
     *     regularDays: int,
     *     regularHours: int,
     *     sundayDays: int,
     *     sundayHours: int,
     *     holidayDays: int,
     *     holidayHours: int,
     *     awayDays: int,
     * }
     */
    public function buildFromArray(array $input): array
    {
        foreach ($input as $workedDay) {
            $this->regularDays++;
            $this->regularHours += min($workedDay['duration'], self::MAX_WORKDAY_HOURS);

            $this->awayDays--;
        }
        return [
            'regularDays' => $this->regularDays,
            'regularHours' => $this->regularHours,
            'sundayDays' => $this->sundayDays,
            'sundayHours' => $this->sundayHours,
            'holidayDays' => $this->holidayDays,
            'holidayHours' => $this->holidayHours,
            'awayDays' => $this->awayDays,
        ];
    }
}
