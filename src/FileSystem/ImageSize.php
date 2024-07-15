<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

class ImageSize
{
    public function __construct(
        protected readonly int $width,
        protected readonly int $height,
        protected readonly int $type,
        protected readonly string $attribute,
        protected readonly string $mime,
        protected readonly ?int $channels = null,
        protected readonly ?int $bits = null,
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
