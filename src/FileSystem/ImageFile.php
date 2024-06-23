<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use Stringable;

/**
 * Image file utility class to throw exceptions when basic operations fail.
 */
class ImageFile implements Stringable
{
    public function __construct(
        protected readonly string $filename
    ) {}

    public function __toString(): string
    {
        return $this->filename;
    }

    /**
     * Wrapper for getimagesize.
     *
     * @throws FileException
     * @todo Support $image_info parameter.
     */
    public function getSize(): ImageSize
    {
        $result = getimagesize($this->filename);
        if ($result === false) {
            throw new FileException(sprintf('Unable to get size of image: "%s"', $this->filename));
        }

        [$width, $height, $type, $attribute] = $result;
        $mime = $result['mime'];
        $channels = $result['channels'] ?? null;
        $bits = $result['bits'] ?? null;

        return new ImageSize($width, $height, $type, $attribute, $mime, $channels, $bits);
    }
}
