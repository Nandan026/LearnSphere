<?php

namespace App\Support;

class Money
{
    public static function add(string $a, string $b, int $scale = 2): string
    {
        return self::run('bcadd', $a, $b, $scale);
    }

    public static function sub(string $a, string $b, int $scale = 2): string
    {
        return self::run('bcsub', $a, $b, $scale);
    }

    public static function mul(string $a, string $b, int $scale = 2): string
    {
        return self::run('bcmul', $a, $b, $scale);
    }

    private static function run(string $fn, string $a, string $b, int $scale): string
    {
        if (function_exists($fn)) {
            return $fn(self::normalize($a), self::normalize($b), $scale);
        }

        return self::fallback($fn, self::normalize($a), self::normalize($b), $scale);
    }

    private static function normalize(string $amount): string
    {
        $amount = trim($amount);

        if ($amount === '' || $amount === '-') {
            return '0';
        }

        return $amount;
    }

    private static function fallback(string $op, string $a, string $b, int $scale): string
    {
        $toInt = static function (string $value, int $scale): int {
            $negative = str_starts_with($value, '-');
            $value = ltrim($value, '-');
            $parts = explode('.', $value, 2);
            $whole = $parts[0] === '' ? '0' : $parts[0];
            $frac = str_pad(substr($parts[1] ?? '', 0, $scale), $scale, '0');
            $int = (int) ($whole.$frac);

            return $negative ? -$int : $int;
        };

        $fromInt = static function (int $value, int $scale): string {
            $negative = $value < 0;
            $value = abs($value);
            $whole = intdiv($value, 10 ** $scale);
            $frac = str_pad((string) ($value % (10 ** $scale)), $scale, '0', STR_PAD_LEFT);
            $result = $scale > 0 ? $whole.'.'.$frac : (string) $whole;

            return $negative ? '-'.$result : $result;
        };

        $left = $toInt($a, $scale);
        $right = $toInt($b, $scale);

        $result = match ($op) {
            'bcadd' => $left + $right,
            'bcsub' => $left - $right,
            'bcmul' => intdiv($left * $right, 10 ** $scale),
            default => throw new \InvalidArgumentException("Unsupported operation [{$op}]."),
        };

        return $fromInt($result, $scale);
    }
}
