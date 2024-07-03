<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Data;

class ArrayUtil
{
    /**
     * @param array<string|int, mixed> $array1
     * @param array<string|int, mixed> $array2
     */
    public static function equal(array $array1, array $array2): bool
    {
        $array1 = self::sortRecursively($array1);
        $array2 = self::sortRecursively($array2);
        return $array1 === $array2;
    }

    /**
     * @param array<string|int, mixed> $array
     * @return array<string|int, mixed>
     */
    public static function sortRecursively(array $array): array
    {
        // Check for numeric or non-numeric sort.
        if (array_keys($array) === range(0, count($array) - 1)) {
            sort($array);
        } else {
            asort($array);
        }

        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = self::sortRecursively($value);
            }
        }

        unset($value);

        return $array;
    }
}
