<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\Data\ArrayUtil;

use DouglasGreen\Utility\Data\ArrayUtil;
use PHPUnit\Framework\TestCase;

class ArrayUtilTest extends TestCase
{
    public function testEqualWithEqualArrays(): void
    {
        $array1 = [
            'a' => 1,
            'b' => [
                'c' => 3,
                'd' => 4,
            ],
        ];
        $array2 = [
            'b' => [
                'd' => 4,
                'c' => 3,
            ],
            'a' => 1,
        ];

        $this->assertTrue(ArrayUtil::equal($array1, $array2));
    }

    public function testEqualWithNonEqualArrays(): void
    {
        $array1 = [
            'a' => 1,
            'b' => [
                'c' => 3,
                'd' => 4,
            ],
        ];
        $array2 = [
            'b' => [
                'c' => 3,
                'd' => 5,
            ],
            'a' => 1,
        ];

        $this->assertFalse(ArrayUtil::equal($array1, $array2));
    }

    public function testSortRecursively(): void
    {
        $array = [3, 2, 1, ['c', 'a', 'b', ['z', 'x', 'y']]];
        $expected = [1, 2, 3, ['a', 'b', 'c', ['x', 'y', 'z']]];

        $this->assertSame($expected, ArrayUtil::sortRecursively($array));
    }

    public function testSortRecursivelyWithMixedValues(): void
    {
        $array = [
            3,
            2,
            1,
            'b' => 'banana',
            'a' => 'apple',
        ];
        $expected = [
            2 => 1,
            1 => 2,
            0 => 3,
            'a' => 'apple',
            'b' => 'banana',
        ];

        $this->assertSame($expected, ArrayUtil::sortRecursively($array));
    }

    public function testSortRecursivelyWithNestedArray(): void
    {
        $array = [
            'b' => [
                'd' => 4,
                'c' => 3,
            ],
            'a' => 1,
        ];
        $expected = [
            'a' => 1,
            'b' => [
                'c' => 3,
                'd' => 4,
            ],
        ];

        $this->assertSame($expected, ArrayUtil::sortRecursively($array));
    }
}
