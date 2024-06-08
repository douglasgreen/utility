<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Tests;

use DouglasGreen\Utility\Exceptions\Data\ValueException;
use DouglasGreen\Utility\Exceptions\Process\ParseException;
use DouglasGreen\Utility\Url;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class UrlTest extends TestCase
{
    protected Url $url;

    public function testInvalidHostThrowsException(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Invalid host: ');
        $this->url->setHost('.abc');
    }

    public function testInvalidPortThrowsException(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Invalid port: -1');
        $this->url->setPort(-1);
    }

    public function testInvalidSchemeThrowsException(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Invalid scheme: "invalid-scheme"');
        $this->url->setScheme('invalid-scheme');
    }

    public function testSetFragment(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Url::class))
            ->getProperty('fragment');
        $property->setAccessible(true);
        $this->url->setFragment($expected);
        self::assertSame($expected, $property->getValue($this->url));
    }

    public function testSetHost(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Url::class))
            ->getProperty('host');
        $property->setAccessible(true);
        $this->url->setHost($expected);
        self::assertSame($expected, $property->getValue($this->url));
    }

    public function testSetParam(): void
    {
        $key = 'newParam';
        $value = 'newValue';
        $property = (new ReflectionClass(Url::class))
            ->getProperty('params');
        $property->setAccessible(true);
        $this->url->setParam($key, $value);
        $params = $property->getValue($this->url);
        self::assertIsArray($params);
        self::assertArrayHasKey($key, $params);
        self::assertSame($value, $params[$key]);
    }

    public function testSetPass(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Url::class))
            ->getProperty('pass');
        $property->setAccessible(true);
        $this->url->setPass($expected);
        self::assertSame($expected, $property->getValue($this->url));
    }

    public function testSetPath(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Url::class))
            ->getProperty('path');
        $property->setAccessible(true);
        $this->url->setPath($expected);
        self::assertSame($expected, $property->getValue($this->url));
    }

    public function testSetPort(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Url::class))
            ->getProperty('port');
        $property->setAccessible(true);
        $this->url->setPort($expected);
        self::assertSame($expected, $property->getValue($this->url));
    }

    public function testSetQuery(): void
    {
        $query = 'newQuery=value';
        $property = (new ReflectionClass(Url::class))
            ->getProperty('params');
        $property->setAccessible(true);
        $this->url->setQuery($query);
        $params = $property->getValue($this->url);
        self::assertIsArray($params);
        self::assertArrayHasKey('newQuery', $params);
        self::assertSame('value', $params['newQuery']);
    }

    public function testSetScheme(): void
    {
        $expected = 'https';
        $property = (new ReflectionClass(Url::class))
            ->getProperty('scheme');
        $property->setAccessible(true);
        $this->url->setScheme($expected);
        self::assertSame($expected, $property->getValue($this->url));
    }

    public function testSetUser(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Url::class))
            ->getProperty('user');
        $property->setAccessible(true);
        $this->url->setUser($expected);
        self::assertSame($expected, $property->getValue($this->url));
    }

    protected function setUp(): void
    {
        $this->url = new Url('http://username:password@hostname:9090/path?arg=value#anchor');
    }
}
