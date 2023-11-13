<?php

namespace Gendiff\Formatters\Plain;

use function Gendiff\Formatters\Stylish\toString;
use function Functional\flatten;

function plainStringify(array $data): string
{
    $result = flatten(iter($data));
    return implode("\n", $result);
}

function iter(array $data, array $keyPath = []): array
{
    $keys = array_keys($data);
    $messages = array_map(function ($key, $val) use ($keyPath, $data) {
        $keyVal = takeKey($key);
        $newKeyPath = array_merge($keyPath, [$keyVal]);

        if (is_array($val) && (str_starts_with($key, ' '))) {
            return iter($val, $newKeyPath);
        }

        if ((str_starts_with($key, ' '))) {
            return null;
        }

        if (str_starts_with($key, '+')) {
            if (array_key_exists('- ' . $keyVal, $data)) {
                return null;
            }
        }

        if (str_starts_with($key, '-')) {
            if (array_key_exists('+ ' . $keyVal, $data)) {
                $valFrom = $val;
                $valTo = $data["+ " . $keyVal];

                return createUpdateMessage($newKeyPath, $valFrom, $valTo);
            }
            return createRemoveMessage($newKeyPath);
        }
        return createAddedMessage($newKeyPath, $val);
    }, $keys, $data);

    return array_values(array_filter($messages, fn($item) => $item !== null));
}

function checkReturnValue(mixed $value): mixed
{
    if ($value === 'null') {
        return 'null';
    }
    if (is_array($value)) {
        return "[complex value]";
    }
    if (is_bool($value)) {
        return toString($value);
    }
    if (is_string($value)) {
        return var_export($value, true);
    }
    return $value;
}

function createAddedMessage(array $keyPath, mixed $value): string
{
    $path = createKeyPath($keyPath);
    return "Property " . "'" . $path . "'" . " was added with value: " . checkReturnValue($value);
}

function createRemoveMessage(array $keyPath): string
{
    $path = createKeyPath($keyPath);
    return "Property " . "'" . $path . "'" . " was removed";
}

function createUpdateMessage(array $keyPath, mixed $valFrom, mixed $valTo): string
{
    $path = createKeyPath($keyPath);
    return "Property " . "'" . $path . "'" . " was updated. " .
    "From " . checkReturnValue($valFrom) . " to " . checkReturnValue($valTo);
}

function takeKey(string $key): string
{
    $symbols = explode(' ', $key);
    return end($symbols);
}

function createKeyPath(array $keyPath): string
{
    return implode('.', flatten($keyPath));
}
