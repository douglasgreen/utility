<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\FileSystem;

use DouglasGreen\Utility\FileSystem\FileException;
use DouglasGreen\Utility\FileSystem\Path;
use DouglasGreen\Utility\FileSystem\PathUtil;
use PHPUnit\Framework\TestCase;

class PathUtilTest extends TestCase
{
    protected string $testDir;

    protected string $testFile;

    protected string $testFileContent;

    public function testAddSubpath(): void
    {
        $subpath = 'subdir';
        $path = PathUtil::addSubpath($this->testDir, $subpath);
        $this->assertSame($this->testDir . DIRECTORY_SEPARATOR . $subpath, $path);
    }

    public function testCalcMd5(): void
    {
        $expectedMd5 = md5($this->testFileContent);
        $this->assertSame($expectedMd5, PathUtil::calcMd5($this->testFile));
    }

    public function testChangeGroup(): void
    {
        $group = filegroup($this->testFile);
        $this->assertNotFalse($group);
        $this->assertInstanceOf(Path::class, PathUtil::changeGroup($this->testFile, $group));
    }

    public function testChangeMode(): void
    {
        $this->assertInstanceOf(Path::class, PathUtil::changeMode($this->testFile, 0o755));
    }

    public function testChangeOwner(): void
    {
        $owner = filegroup($this->testFile);
        $this->assertNotFalse($owner);
        $this->assertInstanceOf(Path::class, PathUtil::changeOwner($this->testFile, $owner));
    }

    public function testCopy(): void
    {
        $target = $this->testDir . DIRECTORY_SEPARATOR . 'copy_test_file.txt';
        $newPath = PathUtil::copy($this->testFile, $target);
        $this->assertFileExists($target);
        $this->assertInstanceOf(Path::class, $newPath);
        unlink($target);
    }

    public function testDelete(): void
    {
        PathUtil::delete($this->testFile);
        $this->assertFileDoesNotExist($this->testFile);
    }

    public function testExists(): void
    {
        $this->assertTrue(PathUtil::exists($this->testFile));
    }

    public function testGetAccessTime(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsInt(PathUtil::getAccessTime($this->testFile));
    }

    public function testGetFileType(): void
    {
        $this->assertSame('data', PathUtil::getFileType($this->testFile));
    }

    public function testGetLinkStats(): void
    {
        $link = $this->testDir . DIRECTORY_SEPARATOR . 'link';
        symlink($this->testFile, $link);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsArray(PathUtil::getLinkStats($link));
        unlink($link);
    }

    public function testGetLinkTarget(): void
    {
        $link = $this->testDir . DIRECTORY_SEPARATOR . 'link';
        symlink($this->testFile, $link);
        $this->assertSame($this->testFile, PathUtil::getLinkTarget($link));
        unlink($link);
    }

    public function testGetMetaChangeTime(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsInt(PathUtil::getMetaChangeTime($this->testFile));
    }

    public function testGetPermissions(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsInt(PathUtil::getPermissions($this->testFile));
    }

    public function testGetStats(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsArray(PathUtil::getStats($this->testFile));
    }

    public function testGetWriteTime(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsInt(PathUtil::getWriteTime($this->testFile));
    }

    public function testIsDirectory(): void
    {
        $this->assertTrue(PathUtil::isDirectory($this->testDir));
    }

    public function testIsExecutable(): void
    {
        $this->assertFalse(PathUtil::isExecutable($this->testFile));
    }

    public function testIsFile(): void
    {
        $this->assertTrue(PathUtil::isFile($this->testFile));
    }

    public function testIsLink(): void
    {
        $link = $this->testDir . DIRECTORY_SEPARATOR . 'link';
        symlink($this->testFile, $link);
        $this->assertTrue(PathUtil::isLink($link));
        unlink($link);
    }

    public function testIsReadable(): void
    {
        $this->assertTrue(PathUtil::isReadable($this->testFile));
    }

    public function testIsSame(): void
    {
        $this->assertTrue(PathUtil::isSame($this->testFile, $this->testFile));
    }

    public function testIsUpload(): void
    {
        $this->assertFalse(PathUtil::isUpload($this->testFile));
    }

    public function testIsWritable(): void
    {
        $this->assertTrue(PathUtil::isWritable($this->testFile));
    }

    public function testLoadAndPrint(): void
    {
        $this->expectOutputString($this->testFileContent);
        PathUtil::loadAndPrint($this->testFile);
    }

    public function testLoadLines(): void
    {
        $lines = PathUtil::loadLines($this->testFile);
        $this->assertSame([$this->testFileContent], $lines);
    }

    public function testLoadString(): void
    {
        $content = PathUtil::loadString($this->testFile);
        $this->assertSame($this->testFileContent, $content);
    }

    public function testMakeDirectory(): void
    {
        $newDir = $this->testDir . DIRECTORY_SEPARATOR . 'new_dir';
        PathUtil::makeDirectory($newDir);
        $this->assertDirectoryExists($newDir);
        rmdir($newDir);
    }

    public function testMakeHardLink(): void
    {
        $linkPath = $this->testDir . DIRECTORY_SEPARATOR . 'hard_link.txt';
        PathUtil::makeHardLink($this->testFile, $linkPath);
        $this->assertFileExists($linkPath);
        $this->assertSame($this->testFileContent, file_get_contents($linkPath));
        unlink($linkPath);
    }

    public function testMakeSymlink(): void
    {
        $linkPath = $this->testDir . DIRECTORY_SEPARATOR . 'symlink.txt';
        PathUtil::makeSymlink($this->testFile, $linkPath);
        $this->assertTrue(is_link($linkPath));
        $this->assertSame($this->testFileContent, file_get_contents($linkPath));
        unlink($linkPath);
    }

    public function testMustExist(): void
    {
        $this->assertInstanceOf(Path::class, PathUtil::mustExist($this->testFile));

        $this->expectException(FileException::class);
        PathUtil::mustExist($this->testDir . DIRECTORY_SEPARATOR . 'non_existent.txt');
    }

    public function testRemoveBase(): void
    {
        $this->assertSame('test_file.txt', PathUtil::removeBase($this->testDir, $this->testFile));

        $otherPath = '/some/other/path/file.txt';
        $this->assertSame($otherPath, PathUtil::removeBase($this->testDir, $otherPath));
    }

    public function testRename(): void
    {
        $newPath = $this->testDir . DIRECTORY_SEPARATOR . 'renamed_file.txt';
        PathUtil::rename($this->testFile, $newPath);
        $this->assertFileDoesNotExist($this->testFile);
        $this->assertFileExists($newPath);
        rename($newPath, $this->testFile);
    }

    public function testSaveStringToFile(): void
    {
        $data = 'New content';
        PathUtil::saveString($this->testFile, $data);

        $this->assertSame($data, file_get_contents($this->testFile));
    }

    public function testSize(): void
    {
        $size = PathUtil::size($this->testFile);

        $this->assertSame(strlen($this->testFileContent), $size);
    }

    public function testTouch(): void
    {
        PathUtil::touch($this->testFile);

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue(true); // If no exception is thrown, the test is successful
    }

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'path_test';
        $this->testFile = $this->testDir . DIRECTORY_SEPARATOR . 'test_file.txt';
        $this->testFileContent = 'This is a test file.';

        if (! file_exists($this->testDir)) {
            mkdir($this->testDir, 0o777, true);
        }

        file_put_contents($this->testFile, $this->testFileContent);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }

        if (file_exists($this->testDir)) {
            rmdir($this->testDir);
        }
    }
}
