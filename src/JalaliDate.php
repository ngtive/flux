<?php

namespace Flux;

use Illuminate\Support\Carbon;

class JalaliDate
{
    protected Carbon $gregorian;

    protected int $year;

    protected int $month;

    protected int $day;

    public function __construct($year = null, $month = null, $day = null)
    {
        if ($year instanceof Carbon) {
            $this->initializeFromGregorian($year);
        } elseif (is_string($year)) {
            $this->initializeFromString($year);
        } elseif ($year !== null && $month !== null && $day !== null) {
            $this->year = (int) $year;
            $this->month = (int) $month;
            $this->day = (int) $day;
            $this->gregorian = $this->convertToGregorian();
        } else {
            $this->initializeFromGregorian(Carbon::now());
        }
    }

    public static function fromGregorian(Carbon $date): self
    {
        return new self($date);
    }

    public static function fromJalali(int $year, int $month, int $day): self
    {
        return new self($year, $month, $day);
    }

    public static function fromString(string $jalaliDate): self
    {
        return new self($jalaliDate);
    }

    protected function initializeFromGregorian(Carbon $date): void
    {
        $this->gregorian = $date;
        $jalali = \Morilog\Jalali\Jalalian::fromCarbon($date);
        $this->year = $jalali->getYear();
        $this->month = $jalali->getMonth();
        $this->day = $jalali->getDay();
    }

    protected function initializeFromString(string $jalaliDate): void
    {
        [$year, $month, $day] = array_map('intval', explode('/', $jalaliDate));
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->gregorian = $this->convertToGregorian();
    }

    protected function convertToGregorian(): Carbon
    {
        $jalali = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/%02d', $this->year, $this->month, $this->day));
        return $jalali->toCarbon();
    }

    public function toGregorian(): Carbon
    {
        return $this->gregorian;
    }

    public function toJalali(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
        ];
    }

    public function format(string $format): string
    {
        $jalali = \Morilog\Jalali\Jalalian::fromCarbon($this->gregorian);
        return $jalali->format($format);
    }

    public function toDateString(): string
    {
        return sprintf('%04d/%02d/%02d', $this->year, $this->month, $this->day);
    }

    public function toDateTimeString(): string
    {
        return $this->toDateString() . ' ' . $this->gregorian->toTimeString();
    }

    public function year(): int
    {
        return $this->year;
    }

    public function month(): int
    {
        return $this->month;
    }

    public function day(): int
    {
        return $this->day;
    }

    public function monthName(): string
    {
        return $this->format('F');
    }

    public function dayOfWeek(): int
    {
        return $this->gregorian->dayOfWeekIso;
    }

    public function dayName(): string
    {
        return $this->format('l');
    }

    public function isToday(): bool
    {
        return $this->gregorian->isToday();
    }

    public function isWeekend(): bool
    {
        return $this->dayOfWeek() === 5;
    }

    public function startOfMonth(): self
    {
        return new self($this->year, $this->month, 1);
    }

    public function endOfMonth(): self
    {
        return new self($this->year, $this->month, $this->daysInMonth());
    }

    public function startOfYear(): self
    {
        return new self($this->year, 1, 1);
    }

    public function endOfYear(): self
    {
        return new self($this->year, 12, 29);
    }

    public function addDays(int $days): self
    {
        return self::fromGregorian($this->gregorian->addDays($days));
    }

    public function subDays(int $days): self
    {
        return self::fromGregorian($this->gregorian->subDays($days));
    }

    public function addMonths(int $months): self
    {
        return self::fromGregorian($this->gregorian->addMonths($months));
    }

    public function subMonths(int $months): self
    {
        return self::fromGregorian($this->gregorian->subMonths($months));
    }

    public function addYears(int $years): self
    {
        return self::fromGregorian($this->gregorian->addYears($years));
    }

    public function subYears(int $years): self
    {
        return self::fromGregorian($this->gregorian->subYears($years));
    }

    public function gt(JalaliDate $date): bool
    {
        return $this->gregorian->gt($date->gregorian);
    }

    public function gte(JalaliDate $date): bool
    {
        return $this->gregorian->gte($date->gregorian);
    }

    public function lt(JalaliDate $date): bool
    {
        return $this->gregorian->lt($date->gregorian);
    }

    public function lte(JalaliDate $date): bool
    {
        return $this->gregorian->lte($date->gregorian);
    }

    public function eq(JalaliDate $date): bool
    {
        return $this->gregorian->eq($date->gregorian);
    }

    public function diffInDays(JalaliDate $date): int
    {
        return $this->gregorian->diffInDays($date->gregorian);
    }

    public function daysInMonth(): int
    {
        $month = $this->month;

        if ($month <= 6) {
            return 31;
        }

        if ($month <= 11) {
            return 30;
        }

        return $this->isLeapYear() ? 30 : 29;
    }

    public function isLeapYear(): bool
    {
        return (int) ((($this->year * 0.125) - floor($this->year * 0.125)) * 8) === 0;
    }

    public function toArray(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
            'gregorian' => $this->gregorian->format('Y-m-d'),
        ];
    }

    public function __toString(): string
    {
        return $this->toDateString();
    }
}
