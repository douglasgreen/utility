<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\Regex;

use DouglasGreen\Utility\Regex\Regex;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testFilteredReplace(): void
    {
        $result = Regex::filteredReplace('/test/', 'replacement', 'test string test');
        $this->assertSame('replacement string replacement', $result);
    }

    public function testFilteredReplaceList(): void
    {
        $result = Regex::filteredReplaceList(
            '/test/',
            'replacement',
            ['test1', 'no match', 'test2']
        );

        // Numeric keys are preserved leaving missing spots where values were filtered out.
        $this->assertSame([
            0 => 'replacement1',
            2 => 'replacement2',
        ], $result);
    }

    public function testHasMatch(): void
    {
        $this->assertTrue(Regex::hasMatch('/test/', 'test string'));
        $this->assertFalse(Regex::hasMatch('/test/', 'no match'));
    }

    public function testMatch(): void
    {
        $result = Regex::match('/test/', 'test string');
        $this->assertSame(['test'], $result);
    }

    public function testMatchAll(): void
    {
        $result = Regex::matchAll('/\d+/', '123 abc 456');
        $this->assertSame([['123', '456']], $result);

        $this->assertSame([], Regex::matchAll('/\d+/', 'no match'));
    }

    public function testMatchAllSetOrder(): void
    {
        $result = Regex::matchAllSetOrder('/(\d+)/', '123 abc 456');
        $this->assertSame([['123', '123'], ['456', '456']], $result);
    }

    public function testMatchAllWithOffsets(): void
    {
        $result = Regex::matchAllWithOffsets('/\d+/', '123 abc 456');
        $this->assertSame([
            0 => [[
                0 => '123',
                1 => 0,
            ], [
                0 => '456',
                1 => 8,
            ]],
        ], $result);
    }

    public function testMatchWithOffsets(): void
    {
        $result = Regex::matchWithOffsets('/test/', 'test string');
        $this->assertSame([
            0 => ['test', 0],
        ], $result);
    }

    public function testReplace(): void
    {
        $result = Regex::replace('/test/', 'replacement', 'test string test');
        $this->assertSame('replacement string replacement', $result);
    }

    public function testReplaceCall(): void
    {
        $result = Regex::replaceCall(
            '/test/',
            fn($match): string => strtoupper((string) $match[0]),
            'test string test'
        );
        $this->assertSame('TEST string TEST', $result);
    }

    public function testReplaceCallList(): void
    {
        $result = Regex::replaceCallList(
            '/test/',
            fn($match): string => strtoupper((string) $match[0]),
            ['test1', 'no match', 'test2']
        );
        $this->assertSame(['TEST1', 'no match', 'TEST2'], $result);
    }

    public function testReplaceList(): void
    {
        $result = Regex::replaceList('/test/', 'replacement', ['test1', 'no match', 'test2']);
        $this->assertSame(['replacement1', 'no match', 'replacement2'], $result);
    }

    public function testSearchList(): void
    {
        $result = Regex::searchList('/test/', ['test1', 'no match', 'test2']);
        $this->assertSame([
            0 => 'test1',
            2 => 'test2',
        ], $result);
    }

    public function testSearchListInverted(): void
    {
        $result = Regex::searchListInverted('/test/', ['test1', 'no match', 'test2']);
        $this->assertSame([
            1 => 'no match',
        ], $result);
    }

    public function testSplit(): void
    {
        $result = Regex::split('/\s+/', 'test string test');
        $this->assertSame(['test', 'string', 'test'], $result);
    }

    public function testSplitAll(): void
    {
        $result = Regex::splitAll('/\s+/', 'test  string  test');
        $this->assertSame(['test', 'string', 'test'], $result);
    }
}
