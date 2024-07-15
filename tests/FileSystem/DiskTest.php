<?php

declare(strict_types=1);

namespace Tests\DouglasGreen\Utility\FileSystem;

use DouglasGreen\Utility\FileSystem\Disk;
use DouglasGreen\Utility\FileSystem\DiskException;
use phpmock\Mock;
use phpmock\MockBuilder;
use PHPUnit\Framework\TestCase;

class DiskTest extends TestCase
{
    protected Disk $disk;

    protected MockBuilder $mockBuilder;

    protected array $mocks = [];

    public function testConstructorAndToString(): void
    {
        $this->assertSame('/test/directory', (string) $this->disk);
    }

    public function testGetFreeSpaceSuccess(): void
    {
        $this->createFunctionMock('disk_free_space', function ($directory): float {
            $this->assertSame('/test/directory', $directory);
            return 1024.0;
        });

        $this->assertEqualsWithDelta(1024.0, $this->disk->getFreeSpace(), PHP_FLOAT_EPSILON);
    }

    public function testGetFreeSpaceFailure(): void
    {
        $this->createFunctionMock('disk_free_space', fn(): bool => false);

        $this->expectException(DiskException::class);
        $this->expectExceptionMessage(
            'Unable to get available space on filesystem: "/test/directory"'
        );

        $this->disk->getFreeSpace();
    }

    public function testGetTotalSpaceSuccess(): void
    {
        $this->createFunctionMock('disk_total_space', function ($directory): float {
            $this->assertSame('/test/directory', $directory);
            return 2048.0;
        });

        $this->assertEqualsWithDelta(2048.0, $this->disk->getTotalSpace(), PHP_FLOAT_EPSILON);
    }

    public function testGetTotalSpaceFailure(): void
    {
        $this->createFunctionMock('disk_total_space', fn(): bool => false);

        $this->expectException(DiskException::class);
        $this->expectExceptionMessage('Unable to get total space on filesystem: "/test/directory"');

        $this->disk->getTotalSpace();
    }

    protected function createFunctionMock(string $functionName, callable $callback): Mock
    {
        $mock = $this->mockBuilder
            ->setNamespace('DouglasGreen\Utility\FileSystem')
            ->setName($functionName)
            ->setFunction($callback)
            ->build();
        $mock->enable();
        $this->mocks[] = $mock;
        return $mock;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->disk = new Disk('/test/directory');
        $this->mockBuilder = new MockBuilder();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->mocks as $mock) {
            $mock->disable();
        }

        $this->mocks = [];
    }
}
