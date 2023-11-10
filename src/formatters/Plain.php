<?php

namespace Gendiff\Formatters\Plain;

use function Gendiff\Formatters\Stylish\toString;

function plainStringify(array $data): string
{
    $result = arrayFlatten(iter($data));
    return implode("\n", $result) . "\n";
}

function iter(array $data, array $keyPath = [], array $passedKeys = []): array
{
    $keys = array_keys($data);
    $messages = array_map(function ($key, $val) use ($keyPath, $data, &$passedKeys) {
        $keyVal = takeKey($key);
        $keyPath[] = $keyVal;

        if (is_array($val) && (str_starts_with($key, ' '))) {
            return iter($val, $keyPath, $passedKeys);
        }

        if ((str_starts_with($key, ' ')) || (in_array($keyVal, $passedKeys, true))) {
            return null;
        }

        $passedKeys[] = $keyVal;

        if (str_starts_with($key, '-')) {
            if (array_key_exists('+ ' . $keyVal, $data)) {
                $valFrom = $val;
                $valTo = $data["+ " . $keyVal];

                return createUpdateMessage($keyPath, $valFrom, $valTo);
            }

            return createRemoveMessage($keyPath);
        }
        return createAddedMessage($keyPath, $val);
    }, $keys, $data);

    return array_values(array_filter($messages, fn($item) => $item !== null));
}

function arrayFlatten(array $array): array
{
    return array_reduce($array, function ($acc, $item) {
        if (is_array($item)) {
            $acc = array_merge($acc, $item);
            return arrayFlatten($acc);
        }
        $acc[] = $item;
        return $acc;
    }, []);
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
    return implode('.', $keyPath);
}
