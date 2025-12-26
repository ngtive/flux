<?php

namespace Flux;

use Carbon\CarbonPeriod;

class JalaliDateRange
{
    protected ?JalaliDate $start;

    protected ?JalaliDate $end;

    protected ?JalaliDateRangePreset $preset;

    protected CarbonPeriod $carbonPeriod;

    public function __construct($start = null, $end = null)
    {
        $this->setDates($start, $end);
    }

    protected function setDates($start, $end): void
    {
        if ($start instanceof JalaliDate) {
            $this->start = $start;
        } elseif ($start instanceof \Illuminate\Support\Carbon) {
            $this->start = JalaliDate::fromGregorian($start);
        } elseif (is_string($start)) {
            $this->start = JalaliDate::fromString($start);
        } else {
            $this->start = null;
        }

        if ($end instanceof JalaliDate) {
            $this->end = $end;
        } elseif ($end instanceof \Illuminate\Support\Carbon) {
            $this->end = JalaliDate::fromGregorian($end);
        } elseif (is_string($end)) {
            $this->end = JalaliDate::fromString($end);
        } else {
            $this->end = null;
        }

        $this->carbonPeriod = new CarbonPeriod(
            $this->start?->toGregorian(),
            $this->end?->toGregorian()
        );
    }

    public function start(): ?JalaliDate
    {
        return $this->start;
    }

    public function end(): ?JalaliDate
    {
        return $this->end;
    }

    public function preset(): ?JalaliDateRangePreset
    {
        return $this->preset;
    }

    public function hasStart(): bool
    {
        return $this->start !== null;
    }

    public function hasEnd(): bool
    {
        return $this->end !== null;
    }

    public function hasPreset(): bool
    {
        return $this->preset !== null;
    }

    public function isNotAllTime(): bool
    {
        return $this->preset !== JalaliDateRangePreset::AllTime;
    }

    public static function fromPreset(JalaliDateRangePreset $preset): self
    {
        if ($preset === JalaliDateRangePreset::AllTime) {
            throw new \Exception('All time date range is not supported via this constructor because it requires a start date. Please use the ::allTime($start) constructor instead.');
        }

        $dates = $preset->dates();
        $instance = new static($dates[0], $dates[1]);
        $instance->preset = $preset;

        return $instance;
    }

    public static function today(): self
    {
        return static::fromPreset(JalaliDateRangePreset::Today);
    }

    public static function yesterday(): self
    {
        return static::fromPreset(JalaliDateRangePreset::Yesterday);
    }

    public static function thisWeek(): self
    {
        return static::fromPreset(JalaliDateRangePreset::ThisWeek);
    }

    public static function lastWeek(): self
    {
        return static::fromPreset(JalaliDateRangePreset::LastWeek);
    }

    public static function last7Days(): self
    {
        return static::fromPreset(JalaliDateRangePreset::Last7Days);
    }

    public static function thisMonth(): self
    {
        return static::fromPreset(JalaliDateRangePreset::ThisMonth);
    }

    public static function lastMonth(): self
    {
        return static::fromPreset(JalaliDateRangePreset::LastMonth);
    }

    public static function thisQuarter(): self
    {
        return static::fromPreset(JalaliDateRangePreset::ThisQuarter);
    }

    public static function lastQuarter(): self
    {
        return static::fromPreset(JalaliDateRangePreset::LastQuarter);
    }

    public static function thisYear(): self
    {
        return static::fromPreset(JalaliDateRangePreset::ThisYear);
    }

    public static function lastYear(): self
    {
        return static::fromPreset(JalaliDateRangePreset::LastYear);
    }

    public static function last14Days(): self
    {
        return static::fromPreset(JalaliDateRangePreset::Last14Days);
    }

    public static function last30Days(): self
    {
        return static::fromPreset(JalaliDateRangePreset::Last30Days);
    }

    public static function last3Months(): self
    {
        return static::fromPreset(JalaliDateRangePreset::Last3Months);
    }

    public static function last6Months(): self
    {
        return static::fromPreset(JalaliDateRangePreset::Last6Months);
    }

    public static function yearToDate(): self
    {
        return static::fromPreset(JalaliDateRangePreset::YearToDate);
    }

    public static function allTime($start): self
    {
        $instance = new static($start, now());
        $instance->preset = JalaliDateRangePreset::AllTime;

        return $instance;
    }

    public function toArray(): array
    {
        return [
            'start' => $this->start?->toDateString(),
            'end' => $this->end?->toDateString(),
            'gregorian_start' => $this->start?->toGregorian()?->format('Y-m-d'),
            'gregorian_end' => $this->end?->toGregorian()?->format('Y-m-d'),
            'preset' => $this->preset?->value,
        ];
    }

    public function toGregorianArray(): array
    {
        return [
            'start' => $this->start?->toGregorian()?->format('Y-m-d'),
            'end' => $this->end?->toGregorian()?->format('Y-m-d'),
        ];
    }

    public function toCarbonPeriod(): CarbonPeriod
    {
        return $this->carbonPeriod;
    }

    public function contains(JalaliDate $date): bool
    {
        if (!$this->hasStart() || !$this->hasEnd()) {
            return false;
        }

        return $date->gte($this->start) && $date->lte($this->end);
    }

    public function length(): int
    {
        return $this->start && $this->end
            ? $this->start->diffInDays($this->end) + 1
            : 0;
    }

    public function days(): int
    {
        return $this->length();
    }

    public function each(callable $callback): void
    {
        foreach ($this->carbonPeriod as $carbonDate) {
            $callback(JalaliDate::fromGregorian($carbonDate));
        }
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->carbonPeriod as $carbonDate) {
            yield JalaliDate::fromGregorian($carbonDate);
        }
    }
}
