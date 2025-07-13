<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\FileSystem;

use Directory as PhpDirectory;
use DouglasGreen\Utility\FileSystem\Directory;
use DouglasGreen\Utility\FileSystem\DirUtil;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

class DirUtilTest extends TestCase
{
    protected string $testDir;

    public function testListFiles(): void
    {
        file_put_contents($this->testDir . '/file1.txt', 'content');
        file_put_contents($this->testDir . '/file2.txt', 'content');
        mkdir($this->testDir . '/subdir');
        file_put_contents($this->testDir . '/subdir/file3.txt', 'content');

        $files = DirUtil::listFiles($this->testDir);
        $this->assertCount(3, $files);
        $this->assertContains($this->testDir . '/file1.txt', $files);
        $this->assertContains($this->testDir . '/file2.txt', $files);
        $this->assertContains($this->testDir . '/subdir/file3.txt', $files);

        // Test when argument is a filename instead of directory.
        $file = $this->testDir . '/file1.txt';
        $files = DirUtil::listFiles($file);
        $this->assertContains($this->testDir . '/file1.txt', $files);
    }

    public function testMake(): void
    {
        $newDir = $this->testDir . '/new_dir';
        DirUtil::make($newDir);
        $this->assertDirectoryExists($newDir);

        // Test when dir already exists.
        $sameDir = $this->testDir . '/new_dir';
        DirUtil::make($sameDir);
        $this->assertDirectoryExists($sameDir);
    }

    public function testMakeRecursive(): void
    {
        $newDir = $this->testDir . '/new_dir/sub_dir';
        DirUtil::makeRecursive($newDir);
        $this->assertDirectoryExists($newDir);

        // Test when dir already exists.
        $sameDir = $this->testDir . '/new_dir/sub_dir';
        DirUtil::makeRecursive($sameDir);
        $this->assertDirectoryExists($sameDir);
    }

    public function testMakeTemp(): void
    {
        $tempFile = DirUtil::makeTemp($this->testDir, 'test');
        $this->assertFileExists($tempFile);
        $this->assertNotEmpty($this->testDir, 'Test dir should not be empty');
        /** @phpstan-ignore argument.type */
        $this->assertStringStartsWith($this->testDir, $tempFile);
    }

    public function testOpen(): void
    {
        $phpDirectory = DirUtil::open($this->testDir);
        $this->assertInstanceOf(PhpDirectory::class, $phpDirectory);
        $phpDirectory->close();
    }

    public function testRemove(): void
    {
        $newDir = $this->testDir . '/new_dir';
        mkdir($newDir);
        DirUtil::remove($newDir);
        $this->assertDirectoryDoesNotExist($newDir);
    }

    public function testRemoveContents(): void
    {
        $subDir = $this->testDir . '/sub_dir';
        mkdir($subDir);
        file_put_contents($subDir . '/file.txt', 'content');

        DirUtil::removeContents($this->testDir);

        $this->assertDirectoryExists($this->testDir);
        $this->assertDirectoryDoesNotExist($subDir);
        $this->assertFileDoesNotExist($subDir . '/file.txt');
    }

    #[DoesNotPerformAssertions]
    public function testRemoveNonExistentDirectory(): void
    {
        DirUtil::remove('/root/impossible_dir');
    }

    public function testRemoveRecursive(): void
    {
        $subDir = $this->testDir . '/sub_dir';
        mkdir($subDir);
        file_put_contents($subDir . '/file.txt', 'content');

        DirUtil::removeRecursive($this->testDir);

        $this->assertDirectoryDoesNotExist($this->testDir);
    }

    public function testScan(): void
    {
        file_put_contents($this->testDir . '/file1.txt', 'content');
        file_put_contents($this->testDir . '/file2.txt', 'content');
        mkdir($this->testDir . '/subdir');

        $contents = DirUtil::scan($this->testDir, Directory::NO_DOT_DIRS);

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

        $contents = DirUtil::scan(
            $this->testDir,
            Directory::NO_DOT_DIRS | Directory::SORT_ASCENDING
        );

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

        $contents = DirUtil::scan(
            $this->testDir,
            Directory::NO_DOT_DIRS | Directory::SORT_DESCENDING
        );

        $this->assertCount(4, $contents);
        $this->assertSame(['file_y.txt', 'file_x.txt', 'dir_z', 'dir_w'], $contents);
    }

    public function testSetCurrent(): void
    {
        $originalDir = getcwd();
        if ($originalDir !== false) {
            DirUtil::setCurrent($this->testDir);
            $currentDir = getcwd();
            if ($currentDir !== false) {
                $this->assertSame($this->testDir, $currentDir);
            }

            chdir($originalDir);
        }
    }

    protected function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        if ($files !== false) {
            $files = array_diff($files, ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . '/' . $file;
                is_dir($path) ? $this->removeDirectory($path) : unlink($path);
            }
        }

        rmdir($dir);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDir = DirUtil::getCurrent() . '/var/tests';
        mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->testDir);
        parent::tearDown();
    }
}
