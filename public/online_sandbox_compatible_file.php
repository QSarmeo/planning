<?php

declare(strict_types=1);

$month = 05;
$year = 2024;

$input = [
    [
        'date' => '07/05',
        'duration' => 2
    ],
    [
        'date' => '15/05',
        'duration' => 7
    ],
    [
        'date' => '16/05',
        'duration' => 6
    ]
];

class DayHelper
{
    public const int MIN_SUNDAY_HOURS = 3;
    public const int MIN_HOLIDAY_HOURS = 4;

    public static function isAWorkedSunday(MonthHelper $monthHelper, string $dateDay, int $duration): bool
    {
        return in_array($dateDay, $monthHelper->getSundays(), strict: true)
            && $duration >= static::MIN_SUNDAY_HOURS;
    }

    public static function isAWorkedHoliday(MonthHelper $monthHelper, string $dateDay, int $duration): bool
    {
        return in_array($dateDay, $monthHelper->getHolidays(), strict: true)
            && $duration >= static::MIN_HOLIDAY_HOURS;
    }
}

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
            $date = date_create_from_format('d/m', $workedDay['date']);
            $cappedDuration = min($workedDay['duration'], self::MAX_WORKDAY_HOURS);

            if (
                $cappedDuration <= 0
                || !$date
                || (int) $date->format('m') !== $month
                || $date->format('d') > $monthHelper->getNumberOfDays()
            ) {
                continue;
            }

            $dateDay = $date->format('d');
            if (DayHelper::isAWorkedHoliday($monthHelper, $dateDay, $workedDay['duration'])) {
                $this->holidayDays++;
                $this->holidayHours += $cappedDuration;
            } elseif (DayHelper::isAWorkedSunday($monthHelper, $dateDay, $workedDay['duration'])) {
                $this->sundayDays++;
                $this->sundayHours += $cappedDuration;
            } else {
                $this->regularDays++;
                $this->regularHours += $cappedDuration;
            }
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

$monthSummary = new MonthSummary();
echo sprintf('Résultats pour le %s/%s', $month, $year);
var_dump($monthSummary->buildFromArray($month, $year, $input));
