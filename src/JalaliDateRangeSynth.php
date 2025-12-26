<?php

namespace Flux;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class JalaliDateRangeSynth extends Synth
{
    public static $key = 'fxjdr';

    static function match($target)
    {
        return is_object($target) && $target instanceof JalaliDateRange;
    }

    static function matchByType($type)
    {
        return $type === JalaliDateRange::class;
    }

    static function unwrapForValidation($target)
    {
        $data = [
            'start' => $target->start()?->toDateString(),
            'end' => $target->end()?->toDateString(),
        ];

        $preset = $target->preset();

        $preset && $data['preset'] = $preset->value;

        return $data;
    }

    static function hydrateFromType($type, $value)
    {
        if ($value === '' || $value === null) {
            return null;
        }

        $preset = $value['preset'] ?? null;

        if ($preset) {
            if ($preset === JalaliDateRangePreset::AllTime->value) {
                return JalaliDateRange::allTime($value['start']);
            }

            return JalaliDateRange::fromPreset(JalaliDateRangePreset::from($preset));
        }

        return new JalaliDateRange($value['start'] ?? null, $value['end'] ?? null);
    }

    function dehydrate($target, $dehydrateChild)
    {
        $data = [
            'start' => $target->start()?->toDateString(),
            'end' => $target->end()?->toDateString(),
        ];

        $preset = $target->preset();

        $preset && $data['preset'] = $preset->value;

        return [$data, []];
    }

    function hydrate($value, $meta)
    {
        if ($value === '' || $value === null) {
            return null;
        }

        $preset = $value['preset'] ?? null;

        if ($preset) {
            if ($preset === JalaliDateRangePreset::AllTime->value) {
                return JalaliDateRange::allTime($value['start']);
            }

            return JalaliDateRange::fromPreset(JalaliDateRangePreset::from($preset));
        }

        return new JalaliDateRange($value['start'] ?? null, $value['end'] ?? null);
    }

    function set(&$target, $key, $value)
    {
        $target = match ($key) {
            'start' => new JalaliDateRange($value, $target->end()),
            'end' => new JalaliDateRange($target->start(), $value),
            'preset' => $value === JalaliDateRangePreset::AllTime->value
                ? JalaliDateRange::allTime($target->start())
                : JalaliDateRange::fromPreset(JalaliDateRangePreset::from($value)),
        };
    }

    function unset(&$target, $key)
    {
        $target = match ($key) {
            'start' => new JalaliDateRange(null, $target->end()),
            'end' => new JalaliDateRange($target->start(), null),
            'preset' => new JalaliDateRange($target->start(), $target->end()),
        };
    }
}
