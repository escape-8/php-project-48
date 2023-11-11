<?php

namespace Differ\Differ;

use function Gendiff\Parsers\parseToData;
use function Gendiff\Formatters\createFormat;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $dataFile1 = parseToData($pathToFile1);
    $dataFile2 = parseToData($pathToFile2);
    $diff = createDiff($dataFile1, $dataFile2);
    return createFormat($diff, $format);
}

function createDiff(array $dataFile1, array $dataFile2): array
{
    $dataSum = array_merge($dataFile1, $dataFile2);
    ksort($dataSum);
    $dataSumKeys = array_keys($dataSum);

    $diff = array_map(function ($key, $valueFileSum) use ($dataFile2, $dataFile1) {
        if (is_array($valueFileSum)) {
            if (array_key_exists($key, $dataFile1) && array_key_exists($key, $dataFile2)) {
                $valueFile1 = checkNull($dataFile1[$key]);
                $valueFile2 = checkNull($dataFile2[$key]);
                if (is_array($valueFile1) && is_array($valueFile2)) {
                    return ['  ' . $key => createDiff($valueFile1, $valueFile2)];
                }
                if (is_array($valueFile1)) {
                    return ['  ' . $key => createDiff($valueFile1, [$valueFile2])];
                }

                return ['  ' . $key => createDiff([$valueFile1], $valueFile2)];
            }
        }

        if (array_key_exists($key, $dataFile1) && array_key_exists($key, $dataFile2)) {
            $valueFile1 = checkNull($dataFile1[$key]);
            $valueFile2 = checkNull($dataFile2[$key]);

            if ($valueFile1 === $valueFile2) {
                return ['  ' . $key => $valueFile1];
            }
            if (is_array($valueFile1)) {
                return array_merge(
                    [],
                    ['- ' . $key => createDiff($valueFile1, $valueFile1)],
                    ['+ ' . $key => $valueFile2]
                );
            }

            if (is_array($valueFile2)) {
                return array_merge(
                    [],
                    ['- ' . $key => $valueFile1],
                    ['+ ' . $key => createDiff($valueFile2, $valueFile2)]
                );
            }

            return array_merge([], ['- ' . $key => $valueFile1], ['+ ' . $key => $valueFile2]);
        }

        if (array_key_exists($key, $dataFile1)) {
            $valueFile1 = $dataFile1[$key];
            if (is_array($valueFile1)) {
                return ['- ' . $key => createDiff($valueFile1, $valueFile1)];
            }
            return ['- ' . $key => $valueFile1];
        }

        $valueFile2 = $dataFile2[$key];
        if (is_array($valueFile2)) {
            return ['+ ' . $key => createDiff($valueFile2, $valueFile2)];
        }
        return ['+ ' . $key => $valueFile2];
    }, $dataSumKeys, $dataSum);

    return array_merge([], ...$diff);
}

function checkNull(mixed $item): mixed
{
    return is_null($item) ? 'null' : $item;
}
