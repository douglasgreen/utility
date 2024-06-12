<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Image;

use DouglasGreen\Utility\Exceptions\FileSystem\ImageFileException;

/**
 * Image file utility class to throw exceptions when basic operations fail.
 */
class ImageFile
{
    public function __construct(
        protected string $filename
    ) {}

    /**
     * Wrapper for getimagesize.
     *
     * @throws ImageFileException
     * @todo Support $image_info parameter.
     */
    public function getSize(): ImageSize
    {
        $result = getimagesize($this->filename);
        if ($result === false) {
            throw new ImageFileException(
                sprintf('Unable to get size of image: "%s"', $this->filename),
            );
        }

        [$width, $height, $type, $attribute] = $result;
        $mime = $result['mime'];
        $channels = $result['channels'] ?? null;
        $bits = $result['bits'] ?? null;

        return new ImageSize($width, $height, $type, $attribute, $mime, $channels, $bits);
    }
}
