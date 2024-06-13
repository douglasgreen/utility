<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Data;

class OneLevelArray
{
    /**
     * @param array<string|int, string|float|int|null> $array
     */
    public function __construct(
        protected array $array
    ) {}

    /**
     * @return array<string|int, string|float|int|null>
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * @return string|float|int|null
     */
    public function getElement(string|int $index)
    {
        return $this->array[$index] ?? null;
    }

    public function setElement(string|int $index, string|float|int|null $value): void
    {
        $this->array[$index] = $value;
    }
}
