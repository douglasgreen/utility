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

    public function testFilteredReplaceListThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->filteredReplaceList('replacement', ['subject']);
    }

    public function testFilteredReplaceThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->filteredReplace('replacement', 'subject');
    }

    public function testHasMatch(): void
    {
        $this->assertTrue($this->matcher->hasMatch('test string'));
        $this->assertFalse($this->matcher->hasMatch('no match'));

        $this->expectException(TypeException::class);
        $invalidMatcher = new Matcher(['/test/', '/invalid/']);
        $invalidMatcher->hasMatch('test');
    }

    public function testHasMatchThrowsTypeException(): void
    {
        $this->expectException(TypeException::class);
        $matcher = new Matcher(['not a string']);
        $matcher->hasMatch('subject');
    }

    public function testHasMatchThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->hasMatch('subject');
    }

    public function testMatch(): void
    {
        $result = $this->matcher->match('test string');
        $this->assertSame(['test'], $result);

        $this->expectException(RegexException::class);
        $invalidMatcher = new Matcher('/invalid(/');
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

    public function testMatchAllSetOrderThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->matchAllSetOrder('subject');
    }

    public function testMatchAllSetOrderThrowsTypeException(): void
    {
        $this->expectException(TypeException::class);
        $matcher = new Matcher(['not a string']);
        $matcher->matchAllSetOrder('subject');
    }

    public function testMatchAllThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->matchAll('subject');
    }

    public function testMatchAllThrowsTypeException(): void
    {
        $this->expectException(TypeException::class);
        $matcher = new Matcher(['not a string']);
        $matcher->matchAll('subject');
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

    public function testMatchAllWithOffsetsThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->matchAllWithOffsets('subject');
    }

    public function testMatchAllWithOffsetsThrowsTypeException(): void
    {
        $this->expectException(TypeException::class);
        $matcher = new Matcher(['not a string']);
        $matcher->matchAllWithOffsets('subject');
    }

    public function testMatchThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->match('subject');
    }

    public function testMatchThrowsTypeException(): void
    {
        $this->expectException(TypeException::class);
        $matcher = new Matcher(['not a string']);
        $matcher->match('subject');
    }

    public function testMatchWithOffsets(): void
    {
        $result = $this->matcher->matchWithOffsets('test string');
        $this->assertSame([
            0 => ['test', 0],
        ], $result);
    }

    public function testMatchWithOffsetsThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->matchWithOffsets('subject');
    }

    public function testMatchWithOffsetsThrowsTypeException(): void
    {
        $this->expectException(TypeException::class);
        $matcher = new Matcher(['not a string']);
        $matcher->matchWithOffsets('subject');
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

    public function testReplaceCallThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->replaceCall(function (): void {}, 'subject');
    }

    public function testReplaceCallList(): void
    {
        $result = $this->matcher->replaceCallList(
            fn($match): string => strtoupper((string) $match[0]),
            ['test1', 'no match', 'test2']
        );
        $this->assertSame(['TEST1', 'no match', 'TEST2'], $result);
    }

    public function testReplaceCallListThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->replaceCallList(function (): void {}, ['subject']);
    }

    public function testReplaceList(): void
    {
        $result = $this->matcher->replaceList('replacement', ['test1', 'no match', 'test2']);
        $this->assertSame(['replacement1', 'no match', 'replacement2'], $result);
    }

    public function testReplaceListThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->replaceList('replacement', ['subject']);
    }

    public function testReplaceThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->replace('replacement', 'subject');
    }

    public function testReplaceThrowsRegexExceptionForArray(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher(['/bad', '/regex']);
        @$matcher->replace('replacement', 'subject');
    }

    public function testSearchList(): void
    {
        $result = $this->matcher->searchList(['test1', 'no match', 'test2']);
        $this->assertSame([
            0 => 'test1',
            2 => 'test2',
        ], $result);
    }

    public function testSearchListThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->searchList(['subject']);
    }

    public function testSearchListThrowsTypeException(): void
    {
        $this->expectException(TypeException::class);
        $matcher = new Matcher(['not a string']);
        $matcher->searchList(['subject']);
    }

    public function testSearchListInverted(): void
    {
        $result = $this->matcher->searchListInverted(['test1', 'no match', 'test2']);
        $this->assertSame([
            1 => 'no match',
        ], $result);
    }

    public function testSearchListInvertedThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->searchListInverted(['subject']);
    }

    public function testSearchListInvertedThrowsTypeException(): void
    {
        $this->expectException(TypeException::class);
        $matcher = new Matcher(['not a string']);
        $matcher->searchListInverted(['subject']);
    }

    public function testSplit(): void
    {
        $matcher = new Matcher('/\s+/');
        $result = $matcher->split('test string test');
        $this->assertSame(['test', 'string', 'test'], $result);

        $result = $matcher->split('test string test', 2);
        $this->assertSame(['test', 'string test'], $result);

        $result = $matcher->split(' test string test ', -1, Matcher::NO_EMPTY);
        $this->assertSame(['test', 'string', 'test'], $result);

        $matcher = new Matcher('/(\s+)/');
        $result = $matcher->split('test string test', -1, Matcher::DELIM_CAPTURE);
        $this->assertSame(['test', ' ', 'string', ' ', 'test'], $result);
    }

    public function testSplitAll(): void
    {
        $matcher = new Matcher('/\s+/');
        $result = $matcher->splitAll('test  string  test');
        $this->assertSame(['test', 'string', 'test'], $result);
    }

    public function testSplitAllThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->splitAll('subject');
    }

    public function testSplitAllThrowsTypeException(): void
    {
        $this->expectException(TypeException::class);
        $matcher = new Matcher(['not a string']);
        $matcher->splitAll('subject');
    }

    public function testSplitThrowsRegexException(): void
    {
        $this->expectException(RegexException::class);
        $matcher = new Matcher('/invalid pattern');
        @$matcher->split('subject');
    }

    public function testSplitThrowsTypeException(): void
    {
        $this->expectException(TypeException::class);
        $matcher = new Matcher(['not a string']);
        $matcher->split('subject');
    }

    protected function setUp(): void
    {
        $this->matcher = new Matcher('/test/');
    }
}
