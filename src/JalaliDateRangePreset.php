<?php

namespace Flux;


use Illuminate\Support\Carbon;

enum JalaliDateRangePreset: string
{
    case Today = 'today';
    case Yesterday = 'yesterday';
    case ThisWeek = 'thisWeek';
    case LastWeek = 'lastWeek';
    case Last7Days = 'last7Days';
    case ThisMonth = 'thisMonth';
    case LastMonth = 'lastMonth';
    case ThisQuarter = 'thisQuarter';
    case LastQuarter = 'lastQuarter';
    case ThisYear = 'thisYear';
    case LastYear = 'lastYear';
    case Last14Days = 'last14Days';
    case Last30Days = 'last30Days';
    case Last3Months = 'last3Months';
    case Last6Months = 'last6Months';
    case YearToDate = 'yearToDate';
    case AllTime = 'allTime';
    case Custom = 'custom';

    public function dates(?JalaliDate $start = null): array
    {
        return match ($this) {
            static::Today => [JalaliDate::fromGregorian(Carbon::now()->startOfDay()), JalaliDate::fromGregorian(Carbon::now()->endOfDay())],
            static::Yesterday => [JalaliDate::fromGregorian(Carbon::now()->subDay()->startOfDay()), JalaliDate::fromGregorian(Carbon::now()->subDay()->endOfDay())],
            static::ThisWeek => [JalaliDate::fromGregorian(Carbon::now()->startOfWeek()), JalaliDate::fromGregorian(Carbon::now()->endOfWeek())],
            static::LastWeek => [JalaliDate::fromGregorian(Carbon::now()->subWeek()->startOfWeek()), JalaliDate::fromGregorian(Carbon::now()->subWeek()->endOfWeek())],
            static::Last7Days => [JalaliDate::fromGregorian(Carbon::now()->subDays(7)->addDay()->startOfDay()), JalaliDate::fromGregorian(Carbon::now()->endOfDay())],
            static::ThisMonth => [JalaliDate::fromGregorian(Carbon::now()->startOfMonth()), JalaliDate::fromGregorian(Carbon::now()->endOfMonth())],
            static::LastMonth => [JalaliDate::fromGregorian(Carbon::now()->startOfMonth()->subMonth()), JalaliDate::fromGregorian(Carbon::now()->startOfMonth()->subMonth()->endOfMonth())],
            static::ThisQuarter => [JalaliDate::fromGregorian(Carbon::now()->startOfQuarter()), JalaliDate::fromGregorian(Carbon::now()->endOfQuarter())],
            static::LastQuarter => [JalaliDate::fromGregorian(Carbon::now()->subQuarter()->startOfQuarter()), JalaliDate::fromGregorian(Carbon::now()->subQuarter()->endOfQuarter())],
            static::ThisYear => [JalaliDate::fromGregorian(Carbon::now()->startOfYear()), JalaliDate::fromGregorian(Carbon::now()->endOfYear())],
            static::LastYear => [JalaliDate::fromGregorian(Carbon::now()->subYear()->startOfYear()), JalaliDate::fromGregorian(Carbon::now()->subYear()->endOfYear())],
            static::Last14Days => [JalaliDate::fromGregorian(Carbon::now()->subDays(14)->addDay()->startOfDay()), JalaliDate::fromGregorian(Carbon::now()->endOfDay())],
            static::Last30Days => [JalaliDate::fromGregorian(Carbon::now()->subDays(30)->addDay()->startOfDay()), JalaliDate::fromGregorian(Carbon::now()->endOfDay())],
            static::Last3Months => [JalaliDate::fromGregorian(Carbon::now()->subMonths(3)->addDay()->startOfDay()), JalaliDate::fromGregorian(Carbon::now()->endOfDay())],
            static::Last6Months => [JalaliDate::fromGregorian(Carbon::now()->subMonths(6)->addDay()->startOfDay()), JalaliDate::fromGregorian(Carbon::now()->endOfDay())],
            static::YearToDate => [JalaliDate::fromGregorian(Carbon::now()->startOfYear()), JalaliDate::fromGregorian(Carbon::now()->endOfDay())],
            static::AllTime => [$start, JalaliDate::fromGregorian(Carbon::now()->endOfDay())],
        };
    }

    public function label(): string
    {
        return match ($this) {
            static::Today => 'امروز',
            static::Yesterday => 'دیروز',
            static::ThisWeek => 'این هفته',
            static::LastWeek => 'هفته گذشته',
            static::Last7Days => '۷ روز گذشته',
            static::ThisMonth => 'این ماه',
            static::LastMonth => 'ماه گذشته',
            static::ThisQuarter => 'این فصل',
            static::LastQuarter => 'فصل گذشته',
            static::ThisYear => 'امسال',
            static::LastYear => 'سال گذشته',
            static::Last14Days => '۱۴ روز گذشته',
            static::Last30Days => '۳۰ روز گذشته',
            static::Last3Months => '۳ ماه گذشته',
            static::Last6Months => '۶ ماه گذشته',
            static::YearToDate => 'از ابتدای سال',
            static::AllTime => 'همه زمان‌ها',
            static::Custom => 'سفارشی',
        };
    }
}
