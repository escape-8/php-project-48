<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseToData(string $path): array
{
    $content = (string)file_get_contents((string)realpath($path));
    $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
    $data = null;
    if ($fileExtension === 'json') {
        $data = json_decode($content, true);
    } elseif ($fileExtension === 'yaml' || $fileExtension === 'yml') {
        $data = json_decode((string)json_encode(Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP)), true);
    }
    return $data;
}
