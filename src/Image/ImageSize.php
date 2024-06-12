<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Image;

class ImageSize
{
    public function __construct(
        protected int $width,
        protected int $height,
        protected int $type,
        protected string $attribute,
        protected string $mime,
        protected ?int $channels = null,
        protected ?int $bits = null,
    ) {}

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getBits(): ?int
    {
        return $this->bits;
    }

    public function getChannels(): ?int
    {
        return $this->channels;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getMime(): string
    {
        return $this->mime;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getWidth(): int
    {
        return $this->width;
    }
}
