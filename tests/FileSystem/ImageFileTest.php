<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\FileSystem;

use DouglasGreen\Utility\FileSystem\DirUtil;
use DouglasGreen\Utility\FileSystem\FileException;
use DouglasGreen\Utility\FileSystem\ImageFile;
use DouglasGreen\Utility\FileSystem\ImageSize;
use PHPUnit\Framework\TestCase;

class ImageFileTest extends TestCase
{
    protected string $invalidImagePath;

    protected string $testDir;

    protected string $validImagePath;

    public function testToString(): void
    {
        $imageFile = new ImageFile($this->validImagePath);
        $this->assertSame($this->validImagePath, (string) $imageFile);
    }

    public function testGetSizeWithInvalidImage(): void
    {
        $imageFile = new ImageFile($this->invalidImagePath);

        $this->expectException(FileException::class);
        $this->expectExceptionMessage(
            'Unable to get size of image: "' . $this->invalidImagePath . '"'
        );

        $imageFile->getSize();
    }

    public function testGetSizeWithValidImage(): void
    {
        $imageFile = new ImageFile($this->validImagePath);
        $imageSize = $imageFile->getSize();

        $this->assertInstanceOf(ImageSize::class, $imageSize);
        $this->assertSame(100, $imageSize->getWidth());
        $this->assertSame(100, $imageSize->getHeight());
        $this->assertSame(IMAGETYPE_JPEG, $imageSize->getType());
        $this->assertSame('image/jpeg', $imageSize->getMime());
    }

    protected function setUp(): void
    {
        $this->testDir = DirUtil::getCurrent() . '/var/test';
        mkdir($this->testDir);
        $this->validImagePath = $this->testDir . '/valid_image.jpg';
        $this->invalidImagePath = $this->testDir . '/invalid_image.txt';

        // Create a sample valid image file
        imagejpeg(imagecreate(100, 100), $this->validImagePath);

        // Create an invalid file
        file_put_contents($this->invalidImagePath, 'This is not an image');
    }

    protected function tearDown(): void
    {
        DirUtil::removeRecursive($this->testDir);
    }
}
