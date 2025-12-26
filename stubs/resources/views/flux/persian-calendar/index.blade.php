@props([
    'value' => null,
    'mode' => 'single',
    'min' => null,
    'max' => null,
    'size' => 'base',
    'startDay' => null,
    'months' => 1,
    'minRange' => null,
    'maxRange' => null,
    'openTo' => null,
    'forceOpenTo' => false,
    'navigation' => true,
    'static' => false,
    'multiple' => false,
    'weekNumbers' => false,
    'selectableHeader' => false,
    'withToday' => false,
    'withInputs' => false,
    'fixedWeeks' => false,
    'locale' => 'fa-IR',
    'unavailable' => null,
])

@php
use Flux\JalaliDate;

$wireModel = $attributes->wire('model');
$hasWireModel = $wireModel !== null;

$mode = $multiple ? 'multiple' : $mode;

$monthsToShow = match($mode) {
    'range' => $months === 1 ? 2 : $months,
    default => $months,
};

$startDay = $startDay ?? (function_exists('locale_get_primary_language') ? locale_get_primary_language($locale) === 'fa' ? 0 : 6 : 0);

$persianMonthNames = [
    'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
    'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
];

$persianDayNames = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];

$persianShortDayNames = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];

$unavailableDates = collect();
if ($unavailable) {
    $unavailableDates = collect(explode(',', $unavailable))
        ->filter()
        ->map(fn($date) => JalaliDate::fromString(trim($date)));
}

function getCalendarData($date, $startDay, $fixedWeeks) {
    $startOfMonth = $date->startOfMonth();
    $endOfMonth = $date->endOfMonth();

    $firstDayOfWeek = $startOfMonth->dayOfWeek();
    $daysInMonth = $date->daysInMonth();

    $startOffset = ($startDay - $firstDayOfWeek + 7) % 7;

    $dates = [];
    $currentDate = $startOfMonth->subDays($startOffset);

    $totalDays = $startOffset + $daysInMonth;
    $weeks = ceil($totalDays / 7);

    if ($fixedWeeks) {
        $weeks = max($weeks, 6);
    }

    for ($i = 0; $i < $weeks * 7; $i++) {
        $dates[] = [
            'date' => clone $currentDate,
            'isCurrentMonth' => $currentDate->month() === $date->month(),
        ];
        $currentDate = $currentDate->addDays(1);
    }

    return $dates;
}
@endphp

<div {{ $attributes->merge(['data-flux-persian-calendar' => true]) }} class="flux-persian-calendar {{ $static ? 'pointer-events-none' : '' }}">
    <div class="flux-persian-calendar-wrapper" dir="rtl">
        @foreach(range(0, $monthsToShow - 1) as $monthOffset)
            @php
                if ($openTo) {
                    $currentMonth = JalaliDate::fromString($openTo)->addMonths($monthOffset);
                } elseif ($value) {
                    $currentMonth = is_string($value)
                        ? JalaliDate::fromString($value)->addMonths($monthOffset)
                        : JalaliDate::fromGregorian(now())->addMonths($monthOffset);
                } else {
                    $currentMonth = JalaliDate::fromGregorian(now())->addMonths($monthOffset);
                }

                $calendarDates = getCalendarData($currentMonth, $startDay, $fixedWeeks);
            @endphp

            <div class="flux-persian-calendar-month">
                @if ($navigation)
                    <div class="flux-persian-calendar-header">
                        <button type="button" class="flux-persian-calendar-nav flux-persian-calendar-nav-prev" wire:click="prevMonth">
                            <flux:icon name="chevron-left" variant="outline" />
                        </button>

                        <div class="flux-persian-calendar-month-year">
                            <span class="flux-persian-calendar-month-name">{{ $persianMonthNames[$currentMonth->month() - 1] }}</span>
                            <span class="flux-persian-calendar-year">{{ $currentMonth->year() }}</span>
                        </div>

                        <button type="button" class="flux-persian-calendar-nav flux-persian-calendar-nav-next" wire:click="nextMonth">
                            <flux:icon name="chevron-right" variant="outline" />
                        </button>
                    </div>
                @else
                    <div class="flux-persian-calendar-header flux-persian-calendar-header-static">
                        <div class="flux-persian-calendar-month-year">
                            <span class="flux-persian-calendar-month-name">{{ $persianMonthNames[$currentMonth->month() - 1] }}</span>
                            <span class="flux-persian-calendar-year">{{ $currentMonth->year() }}</span>
                        </div>
                    </div>
                @endif

                <div class="flux-persian-calendar-weekdays">
                    @if ($weekNumbers)
                        <div class="flux-persian-calendar-weeknumber-cell"></div>
                    @endif

                    @for($i = 0; $i < 7; $i++)
                        <div class="flux-persian-calendar-weekday">
                            {{ $persianShortDayNames[($startDay + $i) % 7] }}
                        </div>
                    @endfor
                </div>

                <div class="flux-persian-calendar-days">
                    @php
                        $weekIndex = 0;
                    @endphp
                    @foreach($calendarDates as $index => $dayData)
                        @php
                            $date = $dayData['date'];
                            $isCurrentMonth = $dayData['isCurrentMonth'];

                            $isSelected = false;
                            $isRangeStart = false;
                            $isRangeEnd = false;
                            $isInRange = false;
                            $isToday = $date->isToday();
                            $isUnavailable = $unavailableDates->contains(fn($d) => $d->eq($date));

                            if ($mode === 'single' && $value) {
                                $isSelected = JalaliDate::fromString($value)->eq($date);
                            } elseif ($mode === 'range' && $value && is_string($value) && str_contains($value, '/')) {
                                [$rangeStart, $rangeEnd] = explode('/', $value);
                                $start = JalaliDate::fromString($rangeStart);
                                $end = JalaliDate::fromString($rangeEnd);
                                $isSelected = $start->eq($date) || $end->eq($date);
                                $isRangeStart = $start->eq($date);
                                $isRangeEnd = $end->eq($date);
                                $isInRange = $date->gt($start) && $date->lt($end);
                            } elseif ($mode === 'multiple' && $value) {
                                $selectedDates = collect(explode(',', $value))
                                    ->filter()
                                    ->map(fn($d) => JalaliDate::fromString(trim($d)));
                                $isSelected = $selectedDates->contains(fn($d) => $d->eq($date));
                            }

                            $isDisabled = !$isCurrentMonth || $isUnavailable;

                            if ($min) {
                                $minDate = $min === 'today' ? JalaliDate::fromGregorian(now()) : JalaliDate::fromString($min);
                                $isDisabled = $isDisabled || $date->lt($minDate);
                            }

                            if ($max) {
                                $maxDate = $max === 'today' ? JalaliDate::fromGregorian(now()) : JalaliDate::fromString($max);
                                $isDisabled = $isDisabled || $date->gt($maxDate);
                            }
                        @endphp

                        @if ($index % 7 === 0 && $weekNumbers)
                            <div class="flux-persian-calendar-weeknumber">
                                {{ $date->toGregorian()->weekOfYear }}
                            </div>
                            @php
                                $weekIndex++;
                            @endphp
                        @endif

                        @if ($static)
                            <div class="flux-persian-calendar-day
                                {{ $isCurrentMonth ? '' : 'flux-persian-calendar-day-other-month' }}
                                {{ $isToday ? 'flux-persian-calendar-day-today' : '' }}
                                {{ $isSelected ? 'flux-persian-calendar-day-selected' : '' }}
                                {{ $isRangeStart ? 'flux-persian-calendar-day-range-start' : '' }}
                                {{ $isRangeEnd ? 'flux-persian-calendar-day-range-end' : '' }}
                                {{ $isInRange ? 'flux-persian-calendar-day-in-range' : '' }}
                            ">
                                {{ $date->day() }}
                            </div>
                        @else
                            <button type="button"
                                class="flux-persian-calendar-day
                                    {{ $isCurrentMonth ? '' : 'flux-persian-calendar-day-other-month' }}
                                    {{ $isToday ? 'flux-persian-calendar-day-today' : '' }}
                                    {{ $isSelected ? 'flux-persian-calendar-day-selected' : '' }}
                                    {{ $isRangeStart ? 'flux-persian-calendar-day-range-start' : '' }}
                                    {{ $isRangeEnd ? 'flux-persian-calendar-day-range-end' : '' }}
                                    {{ $isInRange ? 'flux-persian-calendar-day-in-range' : '' }}
                                    {{ $isDisabled ? 'flux-persian-calendar-day-disabled' : '' }}
                                    flux-persian-calendar-day-{{ $size }}"
                                data-date="{{ $date->toDateString() }}"
                                @if (!$isDisabled)
                                    @if ($hasWireModel)
                                        wire:click="selectDate('{{ $date->toDateString() }}')"
                                    @else
                                        onclick="fluxPersianCalendarSelectDate(this)"
                                    @endif
                                @endif
                                {{ $isDisabled ? 'disabled' : '' }}>
                                {{ $date->day() }}
                            </button>
                        @endif
                    @endforeach
                </div>

                @if ($withToday)
                    <button type="button" class="flux-persian-calendar-today" wire:click="goToToday">
                        امروز
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    @if ($hasWireModel)
        <input type="hidden" wire:model="{{ $wireModel->value() }}" value="{{ $value }}">
    @endif
</div>

@script
<script>
    window.fluxPersianCalendarSelectDate = function(button) {
        const date = button.dataset.date;
        const event = new CustomEvent('flux-persian-calendar:select', {
            detail: { date },
            bubbles: true
        });
        button.dispatchEvent(event);
    }
</script>
@endscript

@once
@push('flux:persian-calendar-styles')
<style>
    .flux-persian-calendar {
        font-family: 'Vazirmatn', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .flux-persian-calendar-wrapper {
        display: flex;
        gap: 1.5rem;
    }

    .flux-persian-calendar-month {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .flux-persian-calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 0.5rem;
    }

    .flux-persian-calendar-header-static {
        justify-content: center;
    }

    .flux-persian-calendar-nav {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border: none;
        background: transparent;
        border-radius: 0.375rem;
        cursor: pointer;
        color: #374151;
        transition: all 0.15s ease;
    }

    .flux-persian-calendar-nav:hover {
        background: #f3f4f6;
    }

    .flux-persian-calendar-month-year {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.125rem;
    }

    .flux-persian-calendar-month-name {
        font-weight: 500;
        font-size: 0.875rem;
        color: #111827;
    }

    .flux-persian-calendar-year {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .flux-persian-calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
    }

    .flux-persian-calendar-weekday {
        font-size: 0.75rem;
        font-weight: 500;
        color: #6b7280;
        padding: 0.25rem 0;
    }

    .flux-persian-calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.25rem;
    }

    .flux-persian-calendar-day {
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.15s ease;
        aspect-ratio: 1;
    }

    .flux-persian-calendar-day-sm {
        font-size: 0.75rem;
        min-width: 1.75rem;
        min-height: 1.75rem;
    }

    .flux-persian-calendar-day-base {
        font-size: 0.875rem;
        min-width: 2rem;
        min-height: 2rem;
    }

    .flux-persian-calendar-day-lg {
        font-size: 1rem;
        min-width: 2.5rem;
        min-height: 2.5rem;
    }

    .flux-persian-calendar-day-xl {
        font-size: 1.125rem;
        min-width: 3rem;
        min-height: 3rem;
    }

    .flux-persian-calendar-day-2xl {
        font-size: 1.25rem;
        min-width: 3.5rem;
        min-height: 3.5rem;
    }

    .flux-persian-calendar-day:hover:not(:disabled) {
        background: #f3f4f6;
    }

    .flux-persian-calendar-day-today {
        font-weight: 600;
        background: #dbeafe;
        color: #1e40af;
    }

    .flux-persian-calendar-day-selected {
        background: #2563eb;
        color: white;
        font-weight: 600;
    }

    .flux-persian-calendar-day-selected:hover {
        background: #1d4ed8;
    }

    .flux-persian-calendar-day-range-start {
        border-radius: 0.375rem 0 0 0.375rem;
    }

    .flux-persian-calendar-day-range-end {
        border-radius: 0 0.375rem 0.375rem 0;
    }

    .flux-persian-calendar-day-in-range {
        background: #dbeafe;
        border-radius: 0;
    }

    .flux-persian-calendar-day-other-month {
        color: #d1d5db;
    }

    .flux-persian-calendar-day-disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    .flux-persian-calendar-weeknumber-cell {
        width: 1.5rem;
    }

    .flux-persian-calendar-weeknumber {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.625rem;
        color: #9ca3af;
        width: 1.5rem;
        height: 100%;
    }

    .flux-persian-calendar-today {
        width: 100%;
        padding: 0.5rem;
        margin-top: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        background: white;
        color: #374151;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .flux-persian-calendar-today:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    @media (prefers-color-scheme: dark) {
        .flux-persian-calendar-nav {
            color: #e5e7eb;
        }

        .flux-persian-calendar-nav:hover {
            background: #374151;
        }

        .flux-persian-calendar-month-name {
            color: #f9fafb;
        }

        .flux-persian-calendar-year {
            color: #9ca3af;
        }

        .flux-persian-calendar-weekday {
            color: #9ca3af;
        }

        .flux-persian-calendar-day:hover:not(:disabled) {
            background: #374151;
        }

        .flux-persian-calendar-day-today {
            background: #1e3a8a;
            color: #93c5fd;
        }

        .flux-persian-calendar-day-selected {
            background: #3b82f6;
            color: white;
        }

        .flux-persian-calendar-day-selected:hover {
            background: #2563eb;
        }

        .flux-persian-calendar-day-in-range {
            background: #1e3a8a;
        }

        .flux-persian-calendar-day-other-month {
            color: #6b7280;
        }

        .flux-persian-calendar-weeknumber {
            color: #6b7280;
        }

        .flux-persian-calendar-today {
            background: #1f2937;
            border-color: #374151;
            color: #e5e7eb;
        }

        .flux-persian-calendar-today:hover {
            background: #374151;
            border-color: #4b5563;
        }
    }
</style>
@endpush
@endonce
