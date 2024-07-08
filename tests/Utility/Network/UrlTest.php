<?php

declare(strict_types=1);

namespace Tests\DouglasGreen\Utility\Network;

use DouglasGreen\Utility\Network\Url;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public static function isEncodedProvider(): Iterator
    {
        yield 'Not encoded' => ['https://example.com/path', false];
        yield 'Encoded with spaces' => ['https://example.com/path%20with%20spaces', true];
        yield 'Encoded with plus' => ['https://example.com/path+with+spaces', true];
        yield 'Partially encoded' => ['https://example.com/path%20with spaces', true];
    }

    public static function encodeProvider(): Iterator
    {
        yield 'Simple URL' => ['https://example.com/path', 'https://example.com/path'];
        yield 'URL with query parameters' => [
            'https://example.com/path?key=value&foo=bar',
            'https://example.com/path?key=value&foo=bar',
        ];
        yield 'URL with special characters' => [
            'https://example.com/path with spaces?key=value with spaces',
            'https://example.com/path%20with%20spaces?key=value+with+spaces',
        ];
        yield 'URL with username and password' => [
            'https://user:pass@example.com/path',
            'https://user:pass@example.com/path',
        ];
        yield 'URL with fragment' => [
            'https://example.com/path#section',
            'https://example.com/path#section',
        ];
        yield 'Complex URL' => [
            'https://user:p@ss@example.com/path with spaces?key=value&foo=bar#section',
            'https://user:p%40ss@example.com/path%20with%20spaces?key=value&foo=bar#section',
        ];
    }

    #[DataProvider('isEncodedProvider')]
    public function testIsEncoded(string $input, bool $expected): void
    {
        $this->assertEquals($expected, Url::isEncoded($input));
    }

    #[DataProvider('encodeProvider')]
    public function testRawEncode(string $input, string $expected): void
    {
        $this->assertSame($expected, Url::encode($input));
    }
}
