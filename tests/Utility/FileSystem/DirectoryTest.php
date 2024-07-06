<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\FileSystem;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use DouglasGreen\Utility\FileSystem\Directory;
use DouglasGreen\Utility\FileSystem\DirectoryException;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    protected string $testDir;

    public function testConstructor(): void
    {
        $dir = new Directory($this->testDir);
        $this->assertSame($this->testDir, (string) $dir);
    }

    public function testListFiles(): void
    {
        $dir = new Directory($this->testDir);
        file_put_contents($this->testDir . '/file1.txt', 'content');
        file_put_contents($this->testDir . '/file2.txt', 'content');
        mkdir($this->testDir . '/subdir');
        file_put_contents($this->testDir . '/subdir/file3.txt', 'content');

        $files = $dir->listFiles();
        $this->assertCount(3, $files);
        $this->assertContains($this->testDir . '/file1.txt', $files);
        $this->assertContains($this->testDir . '/file2.txt', $files);
        $this->assertContains($this->testDir . '/subdir/file3.txt', $files);
    }

    public function testMake(): void
    {
        $newDir = $this->testDir . '/new_dir';
        $dir = new Directory($newDir);
        $dir->make();
        $this->assertDirectoryExists($newDir);
    }

    public function testMakeRecursive(): void
    {
        $newDir = $this->testDir . '/new_dir/sub_dir';
        $dir = new Directory($newDir);
        $dir->makeRecursive();
        $this->assertDirectoryExists($newDir);
    }

    public function testMakeTemp(): void
    {
        $dir = new Directory($this->testDir);
        $tempFile = $dir->makeTemp('test');
        $this->assertFileExists($tempFile);
        $this->assertStringStartsWith($this->testDir, $tempFile);
    }

    public function testMakeThrowsExceptionOnFailure(): void
    {
        $this->expectException(DirectoryException::class);
        $dir = new Directory('/root/impossible_dir');

        // Suppress expected warnings so exception test can pass
        @$dir->make();
    }

    public function testOpen(): void
    {
        $dir = new Directory($this->testDir);
        $directory = $dir->open();
        $this->assertInstanceOf(\Directory::class, $directory);
        $directory->close();
    }

    public function testRemove(): void
    {
        $newDir = $this->testDir . '/new_dir';
        mkdir($newDir);
        $dir = new Directory($newDir);
        $dir->remove();
        $this->assertDirectoryDoesNotExist($newDir);
    }

    public function testRemoveContents(): void
    {
        $subDir = $this->testDir . '/sub_dir';
        mkdir($subDir);
        file_put_contents($subDir . '/file.txt', 'content');

        $dir = new Directory($this->testDir);
        $dir->removeContents();

        $this->assertDirectoryExists($this->testDir);
        $this->assertDirectoryDoesNotExist($subDir);
        $this->assertFileDoesNotExist($subDir . '/file.txt');
    }

    public function testRemoveRecursive(): void
    {
        $subDir = $this->testDir . '/sub_dir';
        mkdir($subDir);
        file_put_contents($subDir . '/file.txt', 'content');

        $dir = new Directory($this->testDir);
        $dir->removeRecursive();

        $this->assertDirectoryDoesNotExist($this->testDir);
    }

    #[DoesNotPerformAssertions]
    public function testRemoveNonExistentDirectory(): void
    {
        $dir = new Directory('/root/non_existent_dir');
        $dir->remove();
    }

    public function testScan(): void
    {
        file_put_contents($this->testDir . '/file1.txt', 'content');
        file_put_contents($this->testDir . '/file2.txt', 'content');
        mkdir($this->testDir . '/subdir');

        $dir = new Directory($this->testDir);
        $contents = $dir->scan(Directory::NO_DOT_DIRS);

        $this->assertCount(3, $contents);
        $this->assertContains('file1.txt', $contents);
        $this->assertContains('file2.txt', $contents);
        $this->assertContains('subdir', $contents);
    }

    public function testScanThrowsExceptionOnFailure(): void
    {
        $this->expectException(DirectoryException::class);
        $dir = new Directory('/root/non_existent_dir');

        // Suppress expected warnings so exception test can pass
        @$dir->scan();
    }

    public function testSetCurrent(): void
    {
        $originalDir = getcwd();
        $dir = new Directory($this->testDir);
        $dir->setCurrent();
        $this->assertSame($this->testDir, getcwd());
        chdir($originalDir);
    }

    public function testSetCurrentThrowsExceptionOnFailure(): void
    {
        $this->expectException(DirectoryException::class);
        $dir = new Directory('/root/non_existent_dir');

        // Suppress expected warnings so exception test can pass
        @$dir->setCurrent();
    }

    protected function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDir = __DIR__ . '/test_directory';
        mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->testDir);
        parent::tearDown();
    }
}
