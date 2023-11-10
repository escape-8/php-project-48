<?php

namespace Gendiff\Formatters;

use function Gendiff\Formatters\Stylish\stringify;

function createFormat(array $data, string $format): string
{
    if ($format === 'stylish') {
        return stringify($data);
    }
    return '';
}
