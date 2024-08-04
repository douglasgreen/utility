<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\Network;

use DouglasGreen\Utility\Data\ValueException;
use DouglasGreen\Utility\FileSystem\FileException;
use DouglasGreen\Utility\FileSystem\Path;
use DouglasGreen\Utility\Network\UrlFetcher;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UrlFetcherTest extends TestCase
{
    public static function invalidUrlProvider(): Iterator
    {
        yield ['not a url'];
        yield ['http://'];
    }

    public static function validUrlProvider(): Iterator
    {
        yield ['https://example.com', 'https://example.com'];
        yield ['http://example.com/path?query=value', 'http://example.com/path?query=value'];
        yield ['https://example.com/encoded%20path', 'https://example.com/encoded path'];
    }

    #[DataProvider('validUrlProvider')]
    public function testCreatesUrlFetcherWithValidUrl(string $url, string $expectedUrl): void
    {
        $urlFetcher = new UrlFetcher($url);
        $this->assertSame($expectedUrl, $urlFetcher->getUrl());
    }

    #[DataProvider('invalidUrlProvider')]
    public function testThrowsExceptionForInvalidUrl(string $url): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Bad URL: ' . $url);
        new UrlFetcher($url);
    }

    public function testDecodesEncodedUrl(): void
    {
        $encodedUrl = 'https://example.com/encoded%20path';
        $urlFetcher = new UrlFetcher($encodedUrl);
        $this->assertSame('https://example.com/encoded path', $urlFetcher->getUrl());
    }

    public function testFetchesPageContent(): void
    {
        $url = 'https://example.com';
        $content = 'Example content';

        $mock = $this->getMockBuilder(UrlFetcher::class)
            ->setConstructorArgs([$url])
            ->getMock();

        $mock->expects($this->once())
            ->method('fetchPage')
            ->willReturn($content);

        $this->assertSame($content, $mock->fetchPage());
    }

    public function testReturnsNullWhenFetchFails(): void
    {
        $url = 'https://nosuchdomainexists.com';

        $pathMock = $this->createMock(Path::class);
        $pathMock->method('loadString')
            ->willThrowException(new FileException('File not found'));

        $mock = $this->getMockBuilder(UrlFetcher::class)
            ->setConstructorArgs([$url])
            ->getMock();

        @$this->assertNull($mock->fetchPage());
    }
}
