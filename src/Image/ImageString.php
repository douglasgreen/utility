<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Image;

use DouglasGreen\Utility\Exceptions\Data\ImageException;

/**
 * Image string utility class to throw exceptions when basic operations fail.
 */
class ImageString
{
    public function __construct(
        protected string $data,
    ) {}

    /**
     * Wrapper for getimagesize.
     *
     * @throws ImageException
     * @todo Support $image_info parameter.
     */
    public function getSize(): ImageSize
    {
        $result = getimagesize($this->data);
        if ($result === false) {
            throw new ImageException('Unable to get size of image');
        }

        [$width, $height, $type, $attribute] = $result;
        $mime = $result['mime'];
        $channels = $result['channels'] ?? null;
        $bits = $result['bits'] ?? null;

        return new ImageSize(
            $width,
            $height,
            $type,
            $attribute,
            $mime,
            $channels,
            $bits
        );
    }
}
