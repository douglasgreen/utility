<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\Regex;

use DouglasGreen\Utility\Regex\ArrayMatcher;
use DouglasGreen\Utility\Regex\RegexException;
use PHPUnit\Framework\TestCase;

class ArrayMatcherTest extends TestCase
{
    public function testConstructor(): void
    {
        $patterns = [
            '/foo/' => fn($matches): string => 'bar',
        ];
        $matcher = new ArrayMatcher($patterns);
        $this->assertInstanceOf(ArrayMatcher::class, $matcher);
    }

    public function testReplaceCallMap(): void
    {
        $patterns = [
            '/foo/' => fn($matches): string => 'bar',
        ];
        $matcher = new ArrayMatcher($patterns);
        $result = $matcher->replaceCallMap('foo baz');
        $this->assertSame('bar baz', $result);
        $this->assertTrue($matcher->matched());
        $this->assertSame(1, $matcher->getCount());
    }

    public function testReplaceCallMapList(): void
    {
        $patterns = [
            '/foo/' => fn($matches): string => 'bar',
        ];
        $matcher = new ArrayMatcher($patterns);
        $result = $matcher->replaceCallMapList(['foo baz', 'foo qux']);
        $this->assertSame(['bar baz', 'bar qux'], $result);
        $this->assertTrue($matcher->matched());
        $this->assertSame(2, $matcher->getCount());
    }

    public function testReplaceCallMapListThrowsExceptionOnError(): void
    {
        $this->expectException(RegexException::class);

        $patterns = [
            '/foo/' => fn($matches): string => 'bar',
            '/(?<unclosed>/' => fn($matches): string => 'error',
        ];
        $matcher = new ArrayMatcher($patterns);
        @$matcher->replaceCallMapList(['foo baz', 'foo qux']);
    }

    public function testReplaceCallMapThrowsExceptionOnError(): void
    {
        $this->expectException(RegexException::class);

        $patterns = [
            '/foo/' => fn($matches): string => 'bar',
            '/(?<unclosed>/' => fn($matches): string => 'error',
        ];
        $matcher = new ArrayMatcher($patterns);
        @$matcher->replaceCallMap('foo baz');
    }
}
