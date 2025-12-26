<?php

namespace Flux;

use Illuminate\Support\Carbon;

if (! function_exists('Flux\jalali_date')) {
    function jalali_date($date = null): JalaliDate
    {
        if ($date === null) {
            return JalaliDate::fromGregorian(now());
        }

        if ($date instanceof Carbon) {
            return JalaliDate::fromGregorian($date);
        }

        if ($date instanceof JalaliDate) {
            return $date;
        }

        if (is_string($date)) {
            return JalaliDate::fromString($date);
        }

        return JalaliDate::fromGregorian(now());
    }
}

if (! function_exists('Flux\jalali_now')) {
    function jalali_now(): JalaliDate
    {
        return JalaliDate::fromGregorian(now());
    }
}

if (! function_exists('Flux\to_jalali')) {
    function to_jalali($date): JalaliDate
    {
        if ($date instanceof Carbon) {
            return JalaliDate::fromGregorian($date);
        }

        if ($date instanceof JalaliDate) {
            return $date;
        }

        if (is_string($date)) {
            return JalaliDate::fromString($date);
        }

        return JalaliDate::fromGregorian(now());
    }
}

if (! function_exists('Flux\to_gregorian')) {
    function to_gregorian($date): Carbon
    {
        if ($date instanceof Carbon) {
            return $date;
        }

        if ($date instanceof JalaliDate) {
            return $date->toGregorian();
        }

        if (is_string($date)) {
            return JalaliDate::fromString($date)->toGregorian();
        }

        return now();
    }
}

if (! function_exists('Flux\jalali_format')) {
    function jalali_format($date, string $format = 'Y/m/d'): string
    {
        $jalaliDate = $date instanceof JalaliDate ? $date : to_jalali($date);

        return $jalaliDate->format($format);
    }
}

if (! function_exists('Flux\jalali_month_name')) {
    function jalali_month_name($month): string
    {
        $persianMonthNames = [
            1 => 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
            'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
        ];

        return $persianMonthNames[$month] ?? '';
    }
}

if (! function_exists('Flux\jalali_day_name')) {
    function jalali_day_name($dayOfWeek): string
    {
        $persianDayNames = [
            0 => 'شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'
        ];

        return $persianDayNames[$dayOfWeek] ?? '';
    }
}

if (! function_exists('Flux\is_jalali_leap_year')) {
    function is_jalali_leap_year($year): bool
    {
        return (int) ((($year * 0.125) - floor($year * 0.125)) * 8) === 0;
    }
}

if (! function_exists('Flux\jalali_days_in_month')) {
    function jalali_days_in_month($month, $year = null): int
    {
        if ($month <= 6) {
            return 31;
        }

        if ($month <= 11) {
            return 30;
        }

        if ($year === null) {
            $year = jalali_now()->year();
        }

        return is_jalali_leap_year($year) ? 30 : 29;
    }
}
