<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\FileSystem;

use DouglasGreen\Utility\FileSystem\Path;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    protected string $testDir;

    protected string $testFile;

    protected string $testFileContent;

    public function testAddSubpath(): void
    {
        $path = new Path($this->testDir);
        $subpath = 'subdir';
        $path->addSubpath($subpath);
        $this->assertSame($this->testDir . DIRECTORY_SEPARATOR . $subpath, (string) $path);
    }

    public function testCalcMd5(): void
    {
        $path = new Path($this->testFile);
        $expectedMd5 = md5($this->testFileContent);
        $this->assertSame($expectedMd5, $path->calcMd5());
    }

    public function testConstruct(): void
    {
        $path = new Path($this->testFile);
        $this->assertSame($this->testFile, (string) $path);
    }

    public function testChangeGroup(): void
    {
        $path = new Path($this->testFile);
        $this->assertInstanceOf(Path::class, $path->changeGroup(filegroup($this->testFile)));
    }

    public function testChangeMode(): void
    {
        $path = new Path($this->testFile);
        $this->assertInstanceOf(Path::class, $path->changeMode(0o755));
    }

    public function testChangeOwner(): void
    {
        $path = new Path($this->testFile);
        $this->assertInstanceOf(Path::class, $path->changeOwner(fileowner($this->testFile)));
    }

    public function testCopy(): void
    {
        $path = new Path($this->testFile);
        $target = $this->testDir . DIRECTORY_SEPARATOR . 'copy_test_file.txt';
        $newPath = $path->copy($target);
        $this->assertFileExists($target);
        $this->assertInstanceOf(Path::class, $newPath);
        unlink($target);
    }

    public function testDelete(): void
    {
        $path = new Path($this->testFile);
        $path->delete();
        $this->assertFileDoesNotExist($this->testFile);
    }

    public function testExists(): void
    {
        $path = new Path($this->testFile);
        $this->assertTrue($path->exists());
    }

    public function testGetAccessTime(): void
    {
        $path = new Path($this->testFile);
        $this->assertIsInt($path->getAccessTime());
    }

    public function testGetFileType(): void
    {
        $path = new Path($this->testFile);
        $this->assertSame('data', $path->getFileType());
    }

    public function testGetFlagChecker(): void
    {
        $flags = Path::APPEND | Path::LOCK;
        $flagChecker = Path::getFlagChecker($flags);

        $this->assertTrue($flagChecker->get('append'));
        $this->assertTrue($flagChecker->get('lock'));
        $this->assertFalse($flagChecker->get('ignoreNewLines'));
    }

    public function testGetLinkStats(): void
    {
        $link = $this->testDir . DIRECTORY_SEPARATOR . 'link';
        symlink($this->testFile, $link);
        $path = new Path($link);
        $this->assertIsArray($path->getLinkStats());
        unlink($link);
    }

    public function testGetLinkTarget(): void
    {
        $link = $this->testDir . DIRECTORY_SEPARATOR . 'link';
        symlink($this->testFile, $link);
        $path = new Path($link);
        $this->assertSame($this->testFile, $path->getLinkTarget());
        unlink($link);
    }

    public function testGetMetaChangeTime(): void
    {
        $path = new Path($this->testFile);
        $this->assertIsInt($path->getMetaChangeTime());
    }

    public function testGetPath(): void
    {
        $path = new Path($this->testFile);
        $this->assertSame($this->testFile, $path->getPath());
    }

    public function testGetPermissions(): void
    {
        $path = new Path($this->testFile);
        $this->assertIsInt($path->getPermissions());
    }

    public function testGetStats(): void
    {
        $path = new Path($this->testFile);
        $this->assertIsArray($path->getStats());
    }

    public function testGetWriteTime(): void
    {
        $path = new Path($this->testFile);
        $this->assertIsInt($path->getWriteTime());
    }

    public function testIsDirectory(): void
    {
        $path = new Path($this->testDir);
        $this->assertTrue($path->isDirectory());
    }

    public function testIsExecutable(): void
    {
        $path = new Path($this->testFile);
        $this->assertFalse($path->isExecutable());
    }

    public function testIsFile(): void
    {
        $path = new Path($this->testFile);
        $this->assertTrue($path->isFile());
    }

    public function testIsLink(): void
    {
        $link = $this->testDir . DIRECTORY_SEPARATOR . 'link';
        symlink($this->testFile, $link);
        $path = new Path($link);
        $this->assertTrue($path->isLink());
        unlink($link);
    }

    public function testIsReadable(): void
    {
        $path = new Path($this->testFile);
        $this->assertTrue($path->isReadable());
    }

    public function testIsSame(): void
    {
        $path1 = new Path($this->testFile);
        $path2 = new Path($this->testFile);
        $this->assertTrue($path1->isSame($path2));
    }

    public function testIsUpload(): void
    {
        $path = new Path($this->testFile);
        $this->assertFalse($path->isUpload());
    }

    public function testIsWritable(): void
    {
        $path = new Path($this->testFile);
        $this->assertTrue($path->isWritable());
    }

    public function testLoadAndPrint(): void
    {
        $path = new Path($this->testFile);
        $this->expectOutputString($this->testFileContent);
        $path->loadAndPrint();
    }

    public function testLoadLines(): void
    {
        $path = new Path($this->testFile);
        $lines = $path->loadLines();
        $this->assertSame([$this->testFileContent], $lines);
    }

    public function testLoadString(): void
    {
        $path = new Path($this->testFile);
        $content = $path->loadString();
        $this->assertSame($this->testFileContent, $content);
    }

    public function testSaveStringToFile(): void
    {
        $path = new Path($this->testFile);
        $data = 'New content';
        $path->saveString($data);

        $this->assertSame($data, file_get_contents($this->testFile));
    }

    public function testSize(): void
    {
        $path = new Path($this->testFile);
        $size = $path->size();

        $this->assertSame(strlen($this->testFileContent), $size);
    }

    public function testTouch(): void
    {
        $path = new Path($this->testFile);
        $path->touch();

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
