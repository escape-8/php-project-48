<?php

namespace Gendiff\Formatters\Stylish;

function toString(mixed $value): string
{
    return trim(var_export($value, true), "'");
}

function stringify(mixed $value, string $replacer = ' ', int $spacesCount = 2): string
{
    if (is_array($value)) {
        return implode("\n", iter($value, $replacer, $spacesCount)) . "\n";
    }
    return toString($value);
}

function iter(array $value, string $replacer = ' ', int $spacesCount = 1, array $accStr = [], int $depth = 0): array
{
    $accStr[] = "{";
    $keys = array_keys($value);

    $newStrings = array_map(function ($key, $val) use ($replacer, $spacesCount, $depth) {
        $depth++;
        $spaceItems = str_repeat($replacer, ($spacesCount * $depth));

        if (is_array($val)) {
            $val = implode("\n", iter($val, $replacer, $spacesCount, $accStr = [], $depth += 1));
            return $spaceItems . $key . ': ' . $val;
        }

        if ($val === "") {
            return $spaceItems . $key . ':' . toString($val);
        }

        return $spaceItems . $key . ': ' . toString($val);
    }, $keys, $value);
    $accStr = array_merge($accStr, $newStrings);
    $accStr[] = str_repeat($replacer, ($spacesCount * $depth)) . '}';
    return $accStr;
}
