<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\Network;

use DouglasGreen\Utility\Data\ValueException;
use DouglasGreen\Utility\Network\UrlBuilder;
use PHPUnit\Framework\TestCase;

class UrlBuilderTest extends TestCase
{
    protected UrlBuilder $urlBuilder;

    public function testBuildAndToString(): void
    {
        $url = 'https://user:pass@example.com:8080/path?param1=value1&param2=value2#fragment';
        $urlBuilder = new UrlBuilder($url);

        $this->assertSame($url, $urlBuilder->build());
        $this->assertSame($url, (string) $urlBuilder);
    }

    public function testDeleteParam(): void
    {
        $urlBuilder = new UrlBuilder('https://example.com?param1=value1&param2=value2');
        $urlBuilder->deleteParam('param1');

        $this->assertFalse($urlBuilder->hasParam('param1'));
        $this->assertTrue($urlBuilder->hasParam('param2'));
        $this->assertSame('https://example.com?param2=value2', $urlBuilder->build());
    }

    public function testGetComponents(): void
    {
        $url = 'https://user:pass@example.com:8080/path?param=value#fragment';
        $urlBuilder = new UrlBuilder($url);

        $this->assertSame('fragment', $urlBuilder->getFragment());
        $this->assertSame('example.com', $urlBuilder->getHost());
        $this->assertSame('pass', $urlBuilder->getPass());
        $this->assertSame('/path', $urlBuilder->getPath());
        $this->assertSame(8080, $urlBuilder->getPort());
        $this->assertSame('https', $urlBuilder->getScheme());
        $this->assertSame('user', $urlBuilder->getUser());
    }

    public function testGetParamAndParamArray(): void
    {
        $urlBuilder = new UrlBuilder(
            'https://example.com?param1=value1&param2[]=value2&param2[]=value3'
        );

        $this->assertSame('value1', $urlBuilder->getParam('param1'));
        $this->assertNull($urlBuilder->getParam('param2'));
        $this->assertSame(['value2', 'value3'], $urlBuilder->getParamArray('param2'));
        $this->assertNull($urlBuilder->getParamArray('param1'));
    }

    public function testGetQuery(): void
    {
        $urlBuilder = new UrlBuilder('https://example.com?param1=value1&param2=value2');

        $this->assertSame('param1=value1&param2=value2', $urlBuilder->getQuery());

        $urlBuilder->deleteParam('param1');
        $this->assertSame('param2=value2', $urlBuilder->getQuery());

        $urlBuilder->deleteParam('param2');
        $this->assertNull($urlBuilder->getQuery());
    }

    public function testHasParam(): void
    {
        $urlBuilder = new UrlBuilder('https://example.com?param1=value1&param2=value2');

        $this->assertTrue($urlBuilder->hasParam('param1'));
        $this->assertTrue($urlBuilder->hasParam('param2'));
        $this->assertFalse($urlBuilder->hasParam('param3'));
    }

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

    public function testIsEqual(): void
    {
        $url1 = new UrlBuilder('https://example.com?param1=value1&param2=value2');
        $url2 = new UrlBuilder('https://example.com?param1=value1&param2=value2');
        $url3 = new UrlBuilder('https://example.com?param1=value1&param3=value3');

        $this->assertTrue($url1->isEqual($url2));
        $this->assertFalse($url1->isEqual($url3));
    }

    public function testSetFragment(): void
    {
        $expected = '42';
        $reflectionProperty = (new \ReflectionClass(UrlBuilder::class))->getProperty('fragment');
        $reflectionProperty->setAccessible(true);

        $this->urlBuilder->setFragment($expected);
        $this->assertSame($expected, $reflectionProperty->getValue($this->urlBuilder));
    }

    public function testSetHost(): void
    {
        $expected = '42';
        $reflectionProperty = (new \ReflectionClass(UrlBuilder::class))->getProperty('host');
        $reflectionProperty->setAccessible(true);

        $this->urlBuilder->setHost($expected);
        $this->assertSame($expected, $reflectionProperty->getValue($this->urlBuilder));
    }

    public function testSetParam(): void
    {
        $key = 'newParam';
        $value = 'newValue';
        $reflectionProperty = (new \ReflectionClass(UrlBuilder::class))->getProperty('params');
        $reflectionProperty->setAccessible(true);

        $this->urlBuilder->setParam($key, $value);
        $params = $reflectionProperty->getValue($this->urlBuilder);
        $this->assertIsArray($params);
        $this->assertArrayHasKey($key, $params);
        $this->assertSame($value, $params[$key]);
    }

    public function testSetParamArray(): void
    {
        $urlBuilder = new UrlBuilder('https://example.com');
        $urlBuilder->setParamArray('param', ['value1', 'value2']);

        $this->assertSame(['value1', 'value2'], $urlBuilder->getParamArray('param'));
        $this->assertSame(
            'https://example.com?param%5B0%5D=value1&param%5B1%5D=value2',
            $urlBuilder->build()
        );
    }

    public function testSetPass(): void
    {
        $expected = '42';
        $reflectionProperty = (new \ReflectionClass(UrlBuilder::class))->getProperty('pass');
        $reflectionProperty->setAccessible(true);

        $this->urlBuilder->setPass($expected);
        $this->assertSame($expected, $reflectionProperty->getValue($this->urlBuilder));
    }

    public function testSetPath(): void
    {
        $expected = '42';
        $reflectionProperty = (new \ReflectionClass(UrlBuilder::class))->getProperty('path');
        $reflectionProperty->setAccessible(true);

        $this->urlBuilder->setPath($expected);
        $this->assertSame($expected, $reflectionProperty->getValue($this->urlBuilder));
    }

    public function testSetPort(): void
    {
        $expected = 42;
        $reflectionProperty = (new \ReflectionClass(UrlBuilder::class))->getProperty('port');
        $reflectionProperty->setAccessible(true);

        $this->urlBuilder->setPort($expected);
        $this->assertSame($expected, $reflectionProperty->getValue($this->urlBuilder));
    }

    public function testSetQuery(): void
    {
        $query = 'newQuery=value';
        $reflectionProperty = (new \ReflectionClass(UrlBuilder::class))->getProperty('params');
        $reflectionProperty->setAccessible(true);

        $this->urlBuilder->setQuery($query);
        $params = $reflectionProperty->getValue($this->urlBuilder);
        $this->assertIsArray($params);
        $this->assertArrayHasKey('newQuery', $params);
        $this->assertSame('value', $params['newQuery']);
    }

    public function testSetScheme(): void
    {
        $expected = 'https';
        $reflectionProperty = (new \ReflectionClass(UrlBuilder::class))->getProperty('scheme');
        $reflectionProperty->setAccessible(true);

        $this->urlBuilder->setScheme($expected);
        $this->assertSame($expected, $reflectionProperty->getValue($this->urlBuilder));
    }

    public function testSetUser(): void
    {
        $expected = '42';
        $reflectionProperty = (new \ReflectionClass(UrlBuilder::class))->getProperty('user');
        $reflectionProperty->setAccessible(true);

        $this->urlBuilder->setUser($expected);
        $this->assertSame($expected, $reflectionProperty->getValue($this->urlBuilder));
    }

    protected function setUp(): void
    {
        $this->urlBuilder = new UrlBuilder(
            'http://username:password@hostname:9090/path?arg=value#anchor',
        );
    }
}
