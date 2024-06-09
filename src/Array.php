<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

class ArrayUtils
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
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = self::sortRecursively($value);
            }
        }

        unset($value);
        ksort($array);
        if (array_keys($array) === range(0, count($array) - 1)) {
            sort($array);
        }

        return $array;
    }
}
