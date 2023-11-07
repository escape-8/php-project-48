<?php

namespace Hexlet\Code\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\Differ\genDiff;

class DifferTest extends TestCase
{
    public function createFixturePath(string $fixtureName): string
    {
        $parts = ['tests', 'fixtures', $fixtureName];
        return (string) realpath(implode('/', $parts));
    }

    public function testPlainDiffer1(): void
    {
        $expected = file_get_contents($this->createFixturePath('testPlainDifferResult1.txt'));
        $pathFile1 = $this->createFixturePath('file1.json');
        $pathFile2 = $this->createFixturePath('file2.json');
        $this->assertEquals($expected, genDiff($pathFile1, $pathFile2));
    }

    public function testPlainDiffer2(): void
    {
        $expected = file_get_contents($this->createFixturePath('testPlainDifferResult2.txt'));
        $pathFile1 = $this->createFixturePath('file3.json');
        $pathFile2 = $this->createFixturePath('file4.json');
        $this->assertEquals($expected, genDiff($pathFile1, $pathFile2));
    }
}
