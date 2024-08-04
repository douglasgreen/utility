<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\FileSystem;

use DouglasGreen\Utility\FileSystem\DirectoryException;
use DouglasGreen\Utility\FileSystem\DirectoryHandle;
use PHPUnit\Framework\TestCase;

class DirectoryHandleTest extends TestCase
{
    protected string $testDir;

    public function testConstructor(): void
    {
        $dirHandle = new DirectoryHandle($this->testDir);
        $this->assertInstanceOf(DirectoryHandle::class, $dirHandle);
    }

    public function testConstructorWithInvalidDirectory(): void
    {
        $this->expectException(DirectoryException::class);
        @new DirectoryHandle('/non/existent/directory');
    }

    public function testConstructorWithNullDirectory(): void
    {
        $dirHandle = new DirectoryHandle();
        $this->assertInstanceOf(DirectoryHandle::class, $dirHandle);
        $this->assertEquals(getcwd(), (string) $dirHandle);
    }

    public function testDestructor(): void
    {
        $dirHandle = new DirectoryHandle($this->testDir);
        $reflection = new \ReflectionClass($dirHandle);
        $reflectionProperty = $reflection->getProperty('handle');
        $reflectionProperty->setAccessible(true);

        $handle = $reflectionProperty->getValue($dirHandle);

        unset($dirHandle);

        $this->assertFalse(is_resource($handle));
    }

    public function testRead(): void
    {
        $dirHandle = new DirectoryHandle($this->testDir);
        $files = [];
        while (($file = $dirHandle->read()) !== null) {
            if ($file !== '.' && $file !== '..') {
                $files[] = $file;
            }
        }

        sort($files);
        $this->assertSame(['file1.txt', 'file2.txt'], $files);
    }

    public function testReadAfterEndOfDirectory(): void
    {
        $dirHandle = new DirectoryHandle($this->testDir);
        while ($dirHandle->read() !== null) {
            // Read all entries
        }

        $this->expectException(DirectoryException::class);
        $dirHandle->read();
    }

    public function testRewind(): void
    {
        $dirHandle = new DirectoryHandle($this->testDir);
        $firstRead = $dirHandle->read();
        $dirHandle->rewind();
        $secondRead = $dirHandle->read();
        $this->assertEquals($firstRead, $secondRead);
    }

    public function testToString(): void
    {
        $dirHandle = new DirectoryHandle($this->testDir);
        $this->assertSame($this->testDir, (string) $dirHandle);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDir = sys_get_temp_dir() . '/directoryhandle_test_' . uniqid();
        mkdir($this->testDir);
        touch($this->testDir . '/file1.txt');
        touch($this->testDir . '/file2.txt');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $files = glob($this->testDir . '/*');
        if ($files !== false) {
            foreach ($files as $file) {
                unlink($file);
            }
        }

        rmdir($this->testDir);
    }
}
