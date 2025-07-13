<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\FileSystem;

use DouglasGreen\Utility\FileSystem\File;
use DouglasGreen\Utility\FileSystem\FileException;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    protected string $testDir;

    protected string $testFile;

    public function testConstructorAndDestructor(): void
    {
        $file = new File($this->testFile, 'w');
        $this->assertFileExists($this->testFile);
        unset($file);
        $this->assertFileExists($this->testFile);
    }

    public function testExceptionOnInvalidFile(): void
    {
        $this->expectException(FileException::class);
        @new File('/path/to/nonexistent/file', 'r');
    }

    public function testExceptionOnInvalidRead(): void
    {
        $file = new File($this->testFile, 'w');
        $this->expectException(FileException::class);
        @$file->read(10);
    }

    public function testExceptionOnInvalidWrite(): void
    {
        file_put_contents($this->testFile, 'example contents');
        $file = new File($this->testFile, 'r');
        $this->expectException(FileException::class);
        @$file->write('This should fail');
    }

    public function testGetExclusiveLock(): void
    {
        // Ensure locking contention.
        $file1 = new File($this->testFile, 'w');
        $file2 = new File($this->testFile, 'w');

        // First file should be able to get the exclusive lock
        $this->assertFalse($file1->getExclusiveLock());

        // Second file should fail to get the exclusive lock and return true
        $this->expectException(FileException::class);
        $this->assertTrue($file2->getExclusiveLock());
    }

    public function testGetFields(): void
    {
        file_put_contents($this->testFile, "field1,field2,field3\n1,2,3");
        $file = new File($this->testFile, 'r');
        $this->assertSame(['field1', 'field2', 'field3'], $file->getFields());
        $this->assertSame(['1', '2', '3'], $file->getFields());
        $this->assertNull($file->getFields());
    }

    public function testGetLine(): void
    {
        file_put_contents($this->testFile, "Line 1\nLine 2\nLine 3");
        $file = new File($this->testFile, 'r');
        $this->assertSame("Line 1\n", $file->getLine());
        $this->assertSame("Line 2\n", $file->getLine());
        $this->assertSame('Line 3', $file->getLine());
        $this->assertNull($file->getLine());
    }

    public function testGetPosition(): void
    {
        file_put_contents($this->testFile, '0123456789');
        $file = new File($this->testFile, 'r');
        $this->assertSame(0, $file->getPosition());
        $file->read(5);
        $this->assertSame(5, $file->getPosition());
    }

    public function testGetSharedLock(): void
    {
        $file1 = new File($this->testFile, 'w');
        $file2 = new File($this->testFile, 'r');

        $this->assertFalse($file1->getSharedLock());
        $this->assertFalse($file2->getSharedLock());
    }

    public function testGetStats(): void
    {
        file_put_contents($this->testFile, '0123456789');
        $file = new File($this->testFile, 'r');
        $stats = $file->getStats();
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsArray($stats);
        $this->assertSame(10, $stats['size']);
    }

    public function testPutFields(): void
    {
        $file = new File($this->testFile, 'w+');
        $file->putFields(['field1', 'field2', 'field3']);
        $file->putFields([1, 2, 3]);
        $file->rewind();
        $this->assertSame(['field1', 'field2', 'field3'], $file->getFields());
        $this->assertSame(['1', '2', '3'], $file->getFields());
    }

    public function testReleaseLock(): void
    {
        $file = new File($this->testFile, 'w');
        $file->getExclusiveLock();
        $this->assertInstanceOf(File::class, $file->releaseLock());
    }

    public function testSeekPosition(): void
    {
        file_put_contents($this->testFile, '0123456789');
        $file = new File($this->testFile, 'r');
        $file->seekPosition(5);
        $this->assertSame('56789', $file->read(5));
    }

    public function testTruncate(): void
    {
        file_put_contents($this->testFile, '0123456789');
        $file = new File($this->testFile, 'r+');
        $file->truncate(5);
        $this->assertSame('01234', file_get_contents($this->testFile));
    }

    public function testWriteAndRead(): void
    {
        $file = new File($this->testFile, 'w+');
        $file->write('Hello, World!');
        $file->rewind();
        $this->assertSame('Hello, World!', $file->read(13));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDir = sys_get_temp_dir() . '/file_test_' . uniqid();
        mkdir($this->testDir);
        $this->testFile = $this->testDir . '/test.txt';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }

        if (is_dir($this->testDir)) {
            rmdir($this->testDir);
        }

        parent::tearDown();
    }
}
