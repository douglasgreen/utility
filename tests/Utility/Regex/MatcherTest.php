<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\Regex;

use DouglasGreen\Utility\Data\TypeException;
use DouglasGreen\Utility\Regex\Matcher;
use DouglasGreen\Utility\Regex\RegexException;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    protected Matcher $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Matcher('/test/');
    }

    public function testFilteredReplace(): void
    {
        $result = $this->matcher->filteredReplace('replacement', 'test string test');
        $this->assertSame('replacement string replacement', $result);
    }

    public function testFilteredReplaceList(): void
    {
        $result = $this->matcher->filteredReplaceList(
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
        $this->assertTrue($this->matcher->hasMatch('test string'));
        $this->assertFalse($this->matcher->hasMatch('no match'));

        $this->expectException(TypeException::class);
        $invalidMatcher = new Matcher(['/test/', '/invalid/']);
        $invalidMatcher->hasMatch('test');
    }

    public function testMatch(): void
    {
        $result = $this->matcher->match('test string');
        $this->assertSame(['test'], $result);

        $this->expectException(RegexException::class);
        $invalidMatcher = new Matcher('/invalid(/');

        // Suppress expected warnings for bad regex so exception test can pass
        @$invalidMatcher->match('test');
    }

    public function testMatchAll(): void
    {
        $matcher = new Matcher('/\d+/');
        $result = $matcher->matchAll('123 abc 456');
        $this->assertSame([['123', '456']], $result);

        $this->assertSame([], $matcher->matchAll('no match'));
    }

    public function testMatchAllSetOrder(): void
    {
        $matcher = new Matcher('/(\d+)/');
        $result = $matcher->matchAllSetOrder('123 abc 456');
        $this->assertSame([['123', '123'], ['456', '456']], $result);
    }

    public function testMatchAllWithOffsets(): void
    {
        $matcher = new Matcher('/\d+/');
        $result = $matcher->matchAllWithOffsets('123 abc 456');
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
        $result = $this->matcher->matchWithOffsets('test string');
        $this->assertSame([
            0 => ['test', 0],
        ], $result);
    }

    public function testReplace(): void
    {
        $result = $this->matcher->replace('replacement', 'test string test');
        $this->assertSame('replacement string replacement', $result);
    }

    public function testReplaceCall(): void
    {
        $result = $this->matcher->replaceCall(
            fn($match): string => strtoupper((string) $match[0]),
            'test string test'
        );
        $this->assertSame('TEST string TEST', $result);
    }

    public function testReplaceCallList(): void
    {
        $result = $this->matcher->replaceCallList(
            fn($match): string => strtoupper((string) $match[0]),
            ['test1', 'no match', 'test2']
        );
        $this->assertSame(['TEST1', 'no match', 'TEST2'], $result);
    }

    public function testReplaceList(): void
    {
        $result = $this->matcher->replaceList('replacement', ['test1', 'no match', 'test2']);
        $this->assertSame(['replacement1', 'no match', 'replacement2'], $result);
    }

    public function testSearchList(): void
    {
        $result = $this->matcher->searchList(['test1', 'no match', 'test2']);
        $this->assertSame([
            0 => 'test1',
            2 => 'test2',
        ], $result);
    }

    public function testSearchListInverted(): void
    {
        $result = $this->matcher->searchListInverted(['test1', 'no match', 'test2']);
        $this->assertSame([
            1 => 'no match',
        ], $result);
    }

    public function testSplit(): void
    {
        $matcher = new Matcher('/\s+/');
        $result = $matcher->split('test string test');
        $this->assertSame(['test', 'string', 'test'], $result);
    }

    public function testSplitAll(): void
    {
        $matcher = new Matcher('/\s+/');
        $result = $matcher->splitAll('test  string  test');
        $this->assertSame(['test', 'string', 'test'], $result);
    }
}
