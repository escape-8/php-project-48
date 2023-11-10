<?php

namespace Hexlet\Code\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\Differ\genDiff;

class GendiffTest extends TestCase
{
    public function createFixturePath(string $fixtureName): string
    {
        $parts = ['tests', 'fixtures', $fixtureName];
        return (string) realpath(implode('/', $parts));
    }

    public function testPlainFiles1(): void
    {
        $expected = file_get_contents($this->createFixturePath('testPlainDifferResult1.txt'));
        $pathFile1 = $this->createFixturePath('file1.json');
        $pathFile2 = $this->createFixturePath('file2.json');
        $this->assertEquals($expected, genDiff($pathFile1, $pathFile2));
    }

    public function testPlainFiles2(): void
    {
        $expected = file_get_contents($this->createFixturePath('testPlainDifferResult2.txt'));
        $pathFile1 = $this->createFixturePath('file3.json');
        $pathFile2 = $this->createFixturePath('file4.json');
        $this->assertEquals($expected, genDiff($pathFile1, $pathFile2));
    }

    public function testPlainFiles3(): void
    {
        $expected = file_get_contents($this->createFixturePath('testPlainDifferResult1.txt'));
        $pathFile1 = $this->createFixturePath('file1.yml');
        $pathFile2 = $this->createFixturePath('file2.yaml');
        $this->assertEquals($expected, genDiff($pathFile1, $pathFile2));
    }

    public function testPlainFiles4(): void
    {
        $expected = file_get_contents($this->createFixturePath('testPlainDifferResult2.txt'));
        $pathFile1 = $this->createFixturePath('file3.yml');
        $pathFile2 = $this->createFixturePath('file4.yaml');
        $this->assertEquals($expected, genDiff($pathFile1, $pathFile2));
    }

    public function testRecursionFiles1(): void
    {
        $expected = file_get_contents($this->createFixturePath('testRecDifferResult1.txt'));
        $pathFile1 = $this->createFixturePath('file1rec.json');
        $pathFile2 = $this->createFixturePath('file2rec.json');
        $this->assertEquals($expected, genDiff($pathFile1, $pathFile2));
    }

    public function testRecursionFiles2(): void
    {
        $expected = file_get_contents($this->createFixturePath('testRecDifferResult1.txt'));
        $pathFile1 = $this->createFixturePath('file1rec.yml');
        $pathFile2 = $this->createFixturePath('file2rec.yaml');
        $this->assertEquals($expected, genDiff($pathFile1, $pathFile2));
    }

    public function testPlainFormatter1(): void
    {
        $expected = file_get_contents($this->createFixturePath('testPlainFormatterResult1.txt'));
        $pathFile1 = $this->createFixturePath('file1rec.json');
        $pathFile2 = $this->createFixturePath('file2rec.json');
        $this->assertEquals($expected, genDiff($pathFile1, $pathFile2, 'plain'));
    }
}
