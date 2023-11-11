<?php

namespace Gendiff\Formatters\Json;

use function Gendiff\Formatters\Plain\takeKey;

function jsonStringify(array $data): string
{
    $jsonData = createJsonData($data);
    return json_encode($jsonData, JSON_PRETTY_PRINT);
}

function createJsonData(array $data): array
{
    return iter($data);
}

function iter(array $data, array $passedKeys = []): array
{
    $keys = array_keys($data);

    $newData = array_map(function ($key, $val) use ($data, &$passedKeys) {
        $keyVal = takeKey($key);

        if (is_array($val)) {
            if (str_starts_with($key, ' ')) {
                return [$keyVal => iter($val, $passedKeys)];
            }
        }

        if (str_starts_with($key, ' ')) {
            return [
                $keyVal => [
                    'type' => 'unchanged',
                    'value' => $val
                ]
            ];
        }

        if (in_array($keyVal, $passedKeys)) {
            return null;
        }

        $passedKeys[] = $keyVal;

        if (str_starts_with($key, '-')) {
            if (array_key_exists('+ ' . $keyVal, $data)) {
                $valFrom = $val;
                $valTo = $data["+ " . $keyVal];

                return [
                    $keyVal => [
                        'type' => 'updated',
                        'startValue' => fixKeys($valFrom),
                        'endValue' => fixKeys($valTo)
                    ]
                ];
            }
            return [
                $keyVal => [
                    'type' => 'removed',
                    'removeValue' => fixKeys($val)
                ]
            ];
        }

        if (is_array($val)) {
            return [
                $keyVal => [
                    'type' => 'added',
                    'addValue' => fixKeys($val)
                ]
            ];
        }
        return [
            $keyVal => [
                'type' => 'added',
                'addValue' => $val
            ]
        ];
    }, $keys, $data);
    $jsonData = array_filter($newData, fn($item) => $item !== null);
    return array_merge([], ...$jsonData);
}

function fixKeys(mixed $data): mixed
{
    if ($data === 'null') {
        return json_decode($data);
    }

    if (!is_array($data)) {
        return $data;
    }

    $keys = array_keys($data);

    $newKeys = array_map(function ($key, $val) {
        if ($val === 'null') {
            $val = json_decode($val);
        }

        $keyVal = takeKey($key);
        if (is_array($val)) {
            return [$keyVal => fixKeys($val)];
        }
        return [$keyVal => $val];
    }, $keys, $data);
    return array_merge([], ...$newKeys);
}
