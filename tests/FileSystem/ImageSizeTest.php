<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\FileSystem;

use PHPUnit\Framework\Attributes\DataProvider;
use DouglasGreen\Utility\FileSystem\ImageSize;
use PHPUnit\Framework\TestCase;

class ImageSizeTest extends TestCase
{
    #[DataProvider('imageSizeProvider')]
    public function testImageSizeGetters(
        int $width,
        int $height,
        int $type,
        string $attribute,
        string $mime,
        ?int $channels,
        ?int $bits
    ): void {
        $imageSize = new ImageSize($width, $height, $type, $attribute, $mime, $channels, $bits);

        $this->assertSame($width, $imageSize->getWidth());
        $this->assertSame($height, $imageSize->getHeight());
        $this->assertSame($type, $imageSize->getType());
        $this->assertSame($attribute, $imageSize->getAttribute());
        $this->assertSame($mime, $imageSize->getMime());
        $this->assertEquals($channels, $imageSize->getChannels());
        $this->assertEquals($bits, $imageSize->getBits());
    }

    public static function imageSizeProvider(): \Iterator
    {
        yield 'JPEG image' => [
            800,
            600,
            IMAGETYPE_JPEG,
            'width="800" height="600"',
            'image/jpeg',
            3,
            8,
        ];
        yield 'PNG image' => [
            1024,
            768,
            IMAGETYPE_PNG,
            'width="1024" height="768"',
            'image/png',
            null,
            8,
        ];
        yield 'GIF image' => [
            320,
            240,
            IMAGETYPE_GIF,
            'width="320" height="240"',
            'image/gif',
            null,
            null,
        ];
    }
}
