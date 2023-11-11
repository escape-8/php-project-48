<?php

namespace Gendiff\Formatters;

use function Gendiff\Formatters\Stylish\stringify;
use function Gendiff\Formatters\Plain\plainStringify;
use function Gendiff\Formatters\Json\jsonStringify;

function createFormat(array $data, string $format): string
{
    if ($format === 'plain') {
        return plainStringify($data);
    }

    if ($format === 'json') {
        return jsonStringify($data);
    }

    return stringify($data);
}
