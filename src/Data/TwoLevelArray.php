<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Data;

class TwoLevelArray
{
    /**
     * @param array<string|int, array<string|int, string|float|int|null>> $array
     */
    public function __construct(
        protected array $array
    ) {}

    /**
     * @return array<string|int, array<string|int, string|float|int|null>>
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * @return string|float|int|null
     */
    public function getElement(string|int $index1, string|int $index2)
    {
        return $this->array[$index1][$index2] ?? null;
    }

    public function getSubarray(string|int $index): ?OneLevelArray
    {
        if (isset($this->array[$index])) {
            return new OneLevelArray($this->array[$index]);
        }

        return null;
    }

    public function setElement(
        string|int $index1,
        string|int $index2,
        string|float|int|null $value
    ): void {
        $this->array[$index1][$index2] = $value;
    }
}
