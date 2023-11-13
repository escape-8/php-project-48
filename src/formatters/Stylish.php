<?php

namespace Gendiff\Formatters\Stylish;

function toString(mixed $value): string
{
    return trim(var_export($value, true), "'");
}

function stringify(mixed $value, string $replacer = ' ', int $spacesCount = 2): string
{
    if (is_array($value)) {
        return implode("\n", iter($value, $replacer, $spacesCount));
    }
    return toString($value);
}

function iter(array $value, string $replacer = ' ', int $spacesCount = 1, int $depth = 0): array
{
    $keys = array_keys($value);
    $newDepth = $depth + 1;

    $newStrings = array_map(function ($key, $val) use ($replacer, $spacesCount, $newDepth) {
        $spaceItems = str_repeat($replacer, ($spacesCount * $newDepth));

        if (is_array($val)) {
            $subDepth = $newDepth + 1;
            $data = implode("\n", iter($val, $replacer, $spacesCount, $subDepth));
            return $spaceItems . $key . ': ' . $data;
        }

        return $spaceItems . $key . ': ' . toString($val);
    }, $keys, $value);

    $openBracket = ["{"];
    $closeBracket = [str_repeat($replacer, ($spacesCount * $depth)) . "}"];

    return array_merge($openBracket, $newStrings, $closeBracket);
}
