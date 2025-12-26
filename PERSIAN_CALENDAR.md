# Persian Calendar Component

A comprehensive Persian (Jalali) calendar component for Flux, providing full support for the Iranian calendar system with beautiful UI and RTL support.

## Installation

1. The `morilog/jalali` package is already included in composer.json
2. Run `composer install` to install the dependency

## Features

- **Full Jalali Calendar Support**: Complete implementation of the Iranian solar calendar
- **Multiple Selection Modes**: Single date, multiple dates, and date range selection
- **RTL Layout**: Built-in right-to-left support for Persian text
- **Persian Typography**: Vazirmatn font integration for beautiful Persian text
- **Month/Day Names**: Native Persian month and day names (فروردین, اردیبهشت, etc.)
- **Date Range Presets**: Quick select common date ranges in Persian
- **Customization**: Extensive props for customization
- **Accessibility**: Keyboard navigation and screen reader support
- **Dark Mode**: Automatic dark mode support
- **Livewire Integration**: Full Livewire wire:model support

## Basic Usage

```blade
<flux:persian-calendar />
```

## Single Date Selection

```blade
<flux:persian-calendar value="1403/10/15" />

<flux:persian-calendar wire:model="date" />
```

In your Livewire component:

```php
use Flux\JalaliDate;

class AppointmentBooking extends Component
{
    public ?JalaliDate $date;

    public function book()
    {
        $gregorianDate = $this->date->toGregorian();
    }
}
```

## Multiple Date Selection

```blade
<flux:persian-calendar multiple />

<flux:persian-calendar multiple wire:model="dates" />
```

In your Livewire component:

```php
public array $dates = [];

public function mount()
{
    $this->dates = [
        '1403/10/15',
        '1403/10/20',
    ];
}
```

## Date Range Selection

```blade
<flux:persian-calendar mode="range" />

<flux:persian-calendar mode="range" wire:model="range" />
```

In your Livewire component:

```php
use Flux\JalaliDateRange;

class Dashboard extends Component
{
    public ?JalaliDateRange $range;

    public function mount()
    {
        $this->range = JalaliDateRange::last30Days();
    }
}
```

## Date Range Presets

Use preset options for quick date range selection:

```blade
<flux:persian-calendar mode="range" :with-today="true" />
```

Available presets:

- `امروز` - Today
- `دیروز` - Yesterday
- `این هفته` - This Week
- `هفته گذشته` - Last Week
- `۷ روز گذشته` - Last 7 Days
- `این ماه` - This Month
- `ماه گذشته` - Last Month
- `این فصل` - This Quarter
- `فصل گذشته` - Last Quarter
- `امسال` - This Year
- `سال گذشته` - Last Year
- `۱۴ روز گذشته` - Last 14 Days
- `۳۰ روز گذشته` - Last 30 Days
- `۳ ماه گذشته` - Last 3 Months
- `۶ ماه گذشته` - Last 6 Months
- `از ابتدای سال` - Year to Date
- `همه زمان‌ها` - All Time

## Props

| Prop | Type | Default | Description |
|------|------|----------|-------------|
| `value` | string | `null` | Selected date(s) in Jalali format (Y/m/d) |
| `mode` | string | `'single'` | Selection mode: `single`, `multiple`, or `range` |
| `min` | string | `null` | Minimum selectable date in Jalali format or "today" |
| `max` | string | `null` | Maximum selectable date in Jalali format or "today" |
| `size` | string | `'base'` | Size variant: `xs`, `sm`, `base`, `lg`, `xl`, `2xl` |
| `startDay` | int | `0` | First day of week (0=Saturday to 6=Friday) |
| `months` | int | `1` | Number of months to display |
| `minRange` | int | `null` | Minimum days for range selection |
| `maxRange` | int | `null` | Maximum days for range selection |
| `openTo` | string | `null` | Date to open to (Jalali format) |
| `forceOpenTo` | bool | `false` | Force calendar to open to `openTo` date |
| `navigation` | bool | `true` | Show month navigation buttons |
| `static` | bool | `false` | Display-only calendar (non-interactive) |
| `multiple` | bool | `false` | Enable multiple date selection |
| `weekNumbers` | bool | `false` | Show week numbers |
| `selectableHeader` | bool | `false` | Show month/year dropdowns |
| `withToday` | bool | `false` | Show "Today" button |
| `withInputs` | bool | `false` | Show manual date inputs |
| `fixedWeeks` | bool | `false` | Show fixed number of weeks per month |
| `locale` | string | `'fa-IR'` | Locale for formatting |
| `unavailable` | string | `null` | Comma-separated unavailable dates |

## Persian Font Integration

Add the Persian font directive to your layout:

```blade
<head>
    @fluxPersianFont
</head>
```

This includes the Vazirmatn font family and applies it to Persian calendar components.

## Helper Functions

Several helper functions are available for Jalali date operations:

```php
// Get current date in Jalali
$jdate = jalali_now();

// Convert a Gregorian date to Jalali
$jdate = to_jalali(now());
echo $jdate->toDateString(); // Output: 1403/10/15

// Convert a Jalali date to Gregorian
$gdate = to_gregorian('1403/10/15');

// Format a date
echo jalali_format(now(), 'Y/m/d'); // Output: 1403/10/15

// Get Persian month name
echo jalali_month_name(10); // Output: دی

// Get Persian day name
echo jalali_day_name(0); // Output: شنبه

// Check if year is a leap year
$isLeap = is_jalali_leap_year(1403);

// Get days in a month
$days = jalali_days_in_month(12, 1403);
```

## JalaliDate Class

The `Flux\JalaliDate` class provides a fluent interface for Jalali date manipulation:

```php
$date = new JalaliDate(1403, 10, 15);

// Get parts
echo $date->year(); // 1403
echo $date->month(); // 10
echo $date->day(); // 15

// Formatting
echo $date->format('Y/m/d'); // 1403/10/15
echo $date->monthName(); // دی
echo $date->dayName(); // شنبه

// Comparison
$date1 = new JalaliDate(1403, 10, 15);
$date2 = new JalaliDate(1403, 10, 20);

echo $date1->lt($date2); // true

// Manipulation
$tomorrow = $date->addDays(1);
$nextMonth = $date->addMonths(1);
$nextYear = $date->addYears(1);

// Conversion
$carbon = $date->toGregorian();
```

## JalaliDateRange Class

The `Flux\JalaliDateRange` class works with date ranges:

```php
$range = new JalaliDateRange('1403/10/01', '1403/10/30');

// Get boundaries
echo $range->start()->toDateString(); // 1403/10/01
echo $range->end()->toDateString(); // 1403/10/30

// Get length
echo $range->length(); // 30

// Iterate
foreach ($range as $day) {
    echo $day->toDateString() . "\n";
}

// Convert to Gregorian for database queries
$gregorianRange = $range->toGregorianArray();
Model::whereBetween('created_at', $gregorianRange)->get();
```

### Preset Methods

```php
// Quick range presets
$today = JalaliDateRange::today();
$yesterday = JalaliDateRange::yesterday();
$thisWeek = JalaliDateRange::thisWeek();
$last7Days = JalaliDateRange::last7Days();
$thisMonth = JalaliDateRange::thisMonth();
$lastMonth = JalaliDateRange::lastMonth();
$last30Days = JalaliDateRange::last30Days();
$thisYear = JalaliDateRange::thisYear();
$yearToDate = JalaliDateRange::yearToDate();
```

## Styling

The component uses Tailwind CSS with RTL support. Custom classes can be added via the class attribute:

```blade
<flux:persian-calendar class="my-custom-class" />
```

### CSS Classes

- `.flux-persian-calendar` - Root container
- `.flux-persian-calendar-wrapper` - Calendar wrapper
- `.flux-persian-calendar-month` - Individual month view
- `.flux-persian-calendar-header` - Month/year header
- `.flux-persian-calendar-day` - Day cell
- `.flux-persian-calendar-day-selected` - Selected day
- `.flux-persian-calendar-day-today` - Today's date
- `.flux-persian-calendar-day-in-range` - Day within range

## Accessibility

- Full keyboard navigation (Arrow keys, Page Up/Down, Home/End)
- ARIA attributes for screen readers
- Focus management
- High contrast colors in all modes

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

## License

Proprietary - Part of Flux UI library

## Support

For issues and feature requests, please visit: https://github.com/livewire/flux
