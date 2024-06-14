<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Tests;

use DouglasGreen\Utility\Data\ValueException;
use DouglasGreen\Utility\Network\UrlBuilder;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    protected UrlBuilder $urlBuilder;

    public function testInvalidHostThrowsException(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Invalid host: ');
        $this->urlBuilder->setHost('.abc');
    }

    public function testInvalidPortThrowsException(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Invalid port: -1');
        $this->urlBuilder->setPort(-1);
    }

    public function testInvalidSchemeThrowsException(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Invalid scheme: "invalid-scheme"');
        $this->urlBuilder->setScheme('invalid-scheme');
    }

    public function testSetFragment(): void
    {
        $expected = '42';
        $property = (new \ReflectionClass(UrlBuilder::class))->getProperty('fragment');
        $property->setAccessible(true);

        $this->urlBuilder->setFragment($expected);
        $this->assertSame($expected, $property->getValue($this->urlBuilder));
    }

    public function testSetHost(): void
    {
        $expected = '42';
        $property = (new \ReflectionClass(UrlBuilder::class))->getProperty('host');
        $property->setAccessible(true);

        $this->urlBuilder->setHost($expected);
        $this->assertSame($expected, $property->getValue($this->urlBuilder));
    }

    public function testSetParam(): void
    {
        $key = 'newParam';
        $value = 'newValue';
        $property = (new \ReflectionClass(UrlBuilder::class))->getProperty('params');
        $property->setAccessible(true);

        $this->urlBuilder->setParam($key, $value);
        $params = $property->getValue($this->urlBuilder);
        $this->assertIsArray($params);
        $this->assertArrayHasKey($key, $params);
        $this->assertSame($value, $params[$key]);
    }

    public function testSetPass(): void
    {
        $expected = '42';
        $property = (new \ReflectionClass(UrlBuilder::class))->getProperty('pass');
        $property->setAccessible(true);

        $this->urlBuilder->setPass($expected);
        $this->assertSame($expected, $property->getValue($this->urlBuilder));
    }

    public function testSetPath(): void
    {
        $expected = '42';
        $property = (new \ReflectionClass(UrlBuilder::class))->getProperty('path');
        $property->setAccessible(true);

        $this->urlBuilder->setPath($expected);
        $this->assertSame($expected, $property->getValue($this->urlBuilder));
    }

    public function testSetPort(): void
    {
        $expected = 42;
        $property = (new \ReflectionClass(UrlBuilder::class))->getProperty('port');
        $property->setAccessible(true);

        $this->urlBuilder->setPort($expected);
        $this->assertSame($expected, $property->getValue($this->urlBuilder));
    }

    public function testSetQuery(): void
    {
        $query = 'newQuery=value';
        $property = (new \ReflectionClass(UrlBuilder::class))->getProperty('params');
        $property->setAccessible(true);

        $this->urlBuilder->setQuery($query);
        $params = $property->getValue($this->urlBuilder);
        $this->assertIsArray($params);
        $this->assertArrayHasKey('newQuery', $params);
        $this->assertSame('value', $params['newQuery']);
    }

    public function testSetScheme(): void
    {
        $expected = 'https';
        $property = (new \ReflectionClass(UrlBuilder::class))->getProperty('scheme');
        $property->setAccessible(true);

        $this->urlBuilder->setScheme($expected);
        $this->assertSame($expected, $property->getValue($this->urlBuilder));
    }

    public function testSetUser(): void
    {
        $expected = '42';
        $property = (new \ReflectionClass(UrlBuilder::class))->getProperty('user');
        $property->setAccessible(true);

        $this->urlBuilder->setUser($expected);
        $this->assertSame($expected, $property->getValue($this->urlBuilder));
    }

    protected function setUp(): void
    {
        $this->urlBuilder = new UrlBuilder(
            'http://username:password@hostname:9090/path?arg=value#anchor'
        );
    }
}
