<?php

declare(strict_types=1);

namespace App;

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
