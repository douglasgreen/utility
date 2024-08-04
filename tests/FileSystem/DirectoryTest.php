<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\FileSystem;

use Directory as PhpDirectory;
use DouglasGreen\Utility\FileSystem\Directory;
use DouglasGreen\Utility\FileSystem\DirectoryException;
use Exception;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    protected string $testDir;

    public function testConstructor(): void
    {
        $dir = new Directory($this->testDir);
        $this->assertSame($this->testDir, (string) $dir);

        // Test when making directory from current directory.
        $dir = new Directory();
        $this->assertSame($dir->getPath(), (string) getcwd());
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

        // Test when argument is a filename instead of directory.
        $dir = new Directory($this->testDir . '/file1.txt');
        $files = $dir->listFiles();
        $this->assertContains($this->testDir . '/file1.txt', $files);
    }

    public function testMake(): void
    {
        $newDir = $this->testDir . '/new_dir';
        $dir = new Directory($newDir);
        $dir->make();
        $this->assertDirectoryExists($newDir);

        // Test when dir already exists.
        $sameDir = $this->testDir . '/new_dir';
        $dir = new Directory($sameDir);
        $dir->make();
        $this->assertDirectoryExists($sameDir);
    }

    public function testMakeRecursive(): void
    {
        $newDir = $this->testDir . '/new_dir/sub_dir';
        $dir = new Directory($newDir);
        $dir->makeRecursive();
        $this->assertDirectoryExists($newDir);

        // Test when dir already exists.
        $sameDir = $this->testDir . '/new_dir/sub_dir';
        $dir = new Directory($sameDir);
        $dir->makeRecursive();
        $this->assertDirectoryExists($sameDir);
    }

    public function testMakeRecursiveThrowsException(): void
    {
        $this->expectException(DirectoryException::class);
        $dir = new Directory('/root/impossible_dir');
        @$dir->makeRecursive();
    }

    public function testMakeTemp(): void
    {
        $dir = new Directory($this->testDir);
        $tempFile = $dir->makeTemp('test');
        $this->assertFileExists($tempFile);
        $this->assertNotEmpty($this->testDir, 'Test dir should not be empty');
        $this->assertStringStartsWith($this->testDir, $tempFile);
    }

    public function testMakeTempThrowsException(): void
    {
        $this->expectException(DirectoryException::class);
        $dir = new Directory('/root/impossible_dir');
        $tempFile = $dir->makeTemp('test');
        var_dump($tempFile);
    }

    public function testMakeThrowsException(): void
    {
        $this->expectException(DirectoryException::class);
        $dir = new Directory('/root/impossible_dir');
        @$dir->make();
    }

    public function testOpen(): void
    {
        $dir = new Directory($this->testDir);
        $phpDirectory = $dir->open();
        $this->assertInstanceOf(PhpDirectory::class, $phpDirectory);
        $phpDirectory->close();
    }

    public function testOpenThrowsException(): void
    {
        $this->expectException(DirectoryException::class);
        $dir = new Directory('/root/impossible_dir');
        @$dir->open();
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

    public function testRemoveNonExistentDirectory(): void
    {
        try {
            $dir = new Directory('/root/non_existent_dir');
            $dir->remove();
            $this->assertTrue(true);
        } catch (Exception) {
            $this->fail('An unexpected exception was thrown');
        }
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

    public function testRemoveThrowsException(): void
    {
        $this->expectException(DirectoryException::class);
        $newDir = $this->testDir . '/new_dir';
        mkdir($newDir);
        file_put_contents($newDir . '/file.txt', 'content');
        $dir = new Directory($newDir);
        @$dir->remove();
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

    public function testScanSortAscending(): void
    {
        // Create test files and directories
        file_put_contents($this->testDir . '/file_b.txt', 'content');
        file_put_contents($this->testDir . '/file_a.txt', 'content');
        mkdir($this->testDir . '/dir_c');
        mkdir($this->testDir . '/dir_d');

        $dir = new Directory($this->testDir);
        $contents = $dir->scan(Directory::NO_DOT_DIRS | Directory::SORT_ASCENDING);

        $this->assertCount(4, $contents);
        $this->assertSame(['dir_c', 'dir_d', 'file_a.txt', 'file_b.txt'], $contents);
    }

    public function testScanSortDescending(): void
    {
        // Create test files and directories
        file_put_contents($this->testDir . '/file_x.txt', 'content');
        file_put_contents($this->testDir . '/file_y.txt', 'content');
        mkdir($this->testDir . '/dir_w');
        mkdir($this->testDir . '/dir_z');

        $dir = new Directory($this->testDir);
        $contents = $dir->scan(Directory::NO_DOT_DIRS | Directory::SORT_DESCENDING);

        $this->assertCount(4, $contents);
        $this->assertSame(['file_y.txt', 'file_x.txt', 'dir_z', 'dir_w'], $contents);
    }

    public function testScanThrowsException(): void
    {
        $this->expectException(DirectoryException::class);
        $dir = new Directory('/root/non_existent_dir');
        @$dir->scan();
    }

    public function testSetCurrent(): void
    {
        $originalDir = getcwd();
        $this->assertNotFalse($originalDir);
        $dir = new Directory($this->testDir);
        $dir->setCurrent();

        $workingDir = getcwd();
        $this->assertNotFalse($workingDir);
        $this->assertSame($this->testDir, $workingDir);
        chdir($originalDir);
    }

    public function testSetCurrentThrowsException(): void
    {
        $this->expectException(DirectoryException::class);
        $dir = new Directory('/root/non_existent_dir');
        @$dir->setCurrent();
    }

    protected function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $result = scandir($dir);
        $this->assertNotFalse($result);
        $files = array_diff($result, ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $dir = new Directory();
        $this->testDir = $dir->getPath() . '/var/test_directory';
        mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->testDir);
        parent::tearDown();
    }
}
