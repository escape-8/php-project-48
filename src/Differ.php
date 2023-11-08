<?php

namespace Gendiff\Differ;

use function Gendiff\Parsers\parseToData;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $dataFile1 = parseToData($pathToFile1);
    $dataFile2 = parseToData($pathToFile2);
    ksort($dataFile1);
    ksort($dataFile2);
    $result = [];
    foreach ($dataFile1 as $key => $valueFile1) {
        if (is_bool($valueFile1)) {
            $valueFile1 = var_export($valueFile1, true);
        }
        if (array_key_exists($key, $dataFile2)) {
            $valueFile2 = $dataFile2[$key];

            if (is_bool($valueFile2)) {
                $valueFile2 = var_export($valueFile2, true);
            }

            if ($valueFile2 === $valueFile1) {
                $result[] = '    ' . $key . ': ' . $valueFile1;
            } else {
                $result[] = '  - ' . $key . ': ' . $valueFile1;
                $result[] = '  + ' . $key . ': ' . $valueFile2;
            }
        } else {
            $result[] = '  - ' . $key . ': ' . $valueFile1;
        }
    }

    foreach ($dataFile2 as $key => $valueFile2) {
        if (is_bool($valueFile2)) {
            $valueFile2 = var_export($valueFile2, true);
        }
        if (!array_key_exists($key, $dataFile1)) {
            $result[] = '  + ' . $key . ': ' . $valueFile2;
        }
    }

    return "{\n" . implode("\n", $result) . "\n}\n";
}
