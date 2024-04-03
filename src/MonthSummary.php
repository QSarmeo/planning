<?php

declare(strict_types=1);

namespace App;

class MonthSummary
{
    public const MAX_WORKDAY_HOURS = 8;

    public function __construct(
        public int $regularDays = 0,
        public int $regularHours = 0,
        public int $sundayDays = 0,
        public int $sundayHours = 0,
        public int $holidayDays = 0,
        public int $holidayHours = 0,
        public int $awayDays = 0,
    ) {
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
    public function buildFromArray(int $month, int $year, array $input): array
    {
        $monthHelper = new MonthHelper($month, $year);
        $this->awayDays = $monthHelper->getNumberOfDays();

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
