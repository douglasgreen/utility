<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Data;

/**
 * Flag processor to turn a list of power of two flags into Booleans.
 *
 * Does error checking that flags are unique and power of two and there are no invalid flags set
 * in the test value.
 * Example usage
 *
 * $flags = [
 *     'flagOne' => 1,
 *     'flagTwo' => 2,
 *     'flagFour' => 4,
 * ];
 * $value = 5;
 * $result = FlagUtil::process($flags, $value);
 * print_r($result);
 */
class FlagUtil
{
    // Function to check if a number is a power of two
    public static function isPowerOfTwo(int $value): bool
    {
        return $value > 0 && ($value & ($value - 1)) === 0;
    }

    /**
     * @param array<string, int> $flags
     * @return array<string, bool>
     */
    public static function process(array $flags, int $value): array
    {
        // Validate the flags
        $uniqueValues = [];
        foreach ($flags as $name => $flag) {
            if (! self::isPowerOfTwo($flag)) {
                throw new ValueException(
                    sprintf('The flag value for "%s" is not a power of two.', $name),
                );
            }

            if (isset($uniqueValues[$flag])) {
                throw new ValueException(sprintf('The flag value for "%s" is not unique.', $name));
            }

            $uniqueValues[$flag] = true;
        }

        // Process the value against the flags
        $result = [];
        foreach ($flags as $name => $flag) {
            $result[$name] = (bool) ($value & $flag);
        }

        // Check for any invalid flags in the value
        $validFlagsSum = array_sum($uniqueValues);
        if (($value & ~$validFlagsSum) !== 0) {
            throw new ValueException(
                'The value contains invalid flags that are not defined in the flags array.',
            );
        }

        return $result;
    }
}
