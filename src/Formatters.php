<?php

namespace Gendiff\Formatters;

use function Gendiff\Formatters\Stylish\stringify;
use function Gendiff\Formatters\Plain\plainStringify;

function createFormat(array $data, string $format): string
{
    if ($format === 'plain') {
        return plainStringify($data);
    }
    return stringify($data);
}
