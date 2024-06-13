<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Data;

class ThreeLevelArray
{
    /**
     * @param array<string|int, array<string|int, array<string|int, string|float|int|null>>> $array
     */
    public function __construct(
        protected array $array
    ) {}

    /**
     * @return array<string|int, array<string|int, array<string|int, string|float|int|null>>>
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * @return string|float|int|null
     */
    public function getElement(string|int $index1, string|int $index2, string|int $index3)
    {
        return $this->array[$index1][$index2][$index3] ?? null;
    }

    public function getSubarray(string|int $index): ?TwoLevelArray
    {
        if (isset($this->array[$index])) {
            return new TwoLevelArray($this->array[$index]);
        }

        return null;
    }

    public function setElement(
        string|int $index1,
        string|int $index2,
        string|int $index3,
        string|float|int|null $value
    ): void {
        $this->array[$index1][$index2][$index3] = $value;
    }
}
