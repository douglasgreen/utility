<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Data;

/**
 * Flag processor to turn a list of power of two flags into Booleans.
 *
 * Does error checking that flags are unique and power of two and there are no invalid flags set
 * in the test value.
 *
 * This class exists because integer flags are better than booleans. When you pass booleans to a
 * function parameter, they're not descriptive. Your function calls look like "example(true, false,
 * true, true)". This is error-prone because one boolean can easily substitute for another. It also
 * fails to tell you what it is configuring. Instead you should define flags as power of two integer
 * constants on your class. There should be less than 32 Flags in a class because that is the limit
 * on 32-bit systems running PHP. Then you can pass them to your function like
 * "example(MyClass::RECURSIVE | MyClass::DEPTH_FIRST)". The names describe better what you're doing
 * and are less error prone. And they don't need to be passed in a particular order.
 *
 * The flags and flag checker should be public for other classes to use.
 *
 * Example usage:
 *
 * use DouglasGreen\Utility\Data\FlagChecker;
 * use DouglasGreen\Utility\Data\FlagHandler;
 *
 * class MyClass implements FlagHandler {
 *     public const int RECURSIVE = 1;
 *     public const int DEPTH_FIRST = 2;
 *
 *     // This function gives names to the flag integers of this class. Every class that defines
 *     // flags needs one of these.
 *     public static function getFlagChecker(int $flags): FlagChecker
 *     {
 *         $flagNames = [
 *             'recursive' => self::RECURSIVE,
 *             'depthFirst' => self::DEPTH_FIRST,
 *         ];
 *         return new FlagChecker($flagNames, $flags);
 *     }
 *
 *     // This function uses the flag checker to turn its flag integers into booleans.
 *     protected function myFunc(int $flags): void
 *     {
 *         $flagChecker = static::getFlagChecker($flags);
 *         $isRecursive = $flagChecker->get('recursive');
 *     }
 * }
 */
class FlagChecker
{
    /**
     * @var array<string, bool>
     */
    protected readonly array $settings;

    /**
     * @param array<string, int> $flagNames
     * @throws ValueException
     */
    public function __construct(array $flagNames, int $flags)
    {
        // Limited to 32 bits in a word on 32-bit systems.
        if (count($flagNames) > 32) {
            throw new ValueException(sprintf('More than 32 flags defined: %d', count($flagNames)));
        }

        // Validate the flags
        $uniqueValues = [];
        foreach ($flagNames as $name => $flag) {
            if (! static::isPowerOfTwo($flag)) {
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
        $settings = [];
        foreach ($flagNames as $name => $flag) {
            $settings[$name] = (bool) ($flags & $flag);
        }

        $this->settings = $settings;

        // Check for any invalid flags in the value
        $validFlagsSum = array_sum(array_keys($uniqueValues));
        if (($flags & ~$validFlagsSum) !== 0) {
            throw new ValueException(
                'The value contains invalid flags that are not defined in the flags array.',
            );
        }
    }

    public function get(string $name): bool
    {
        if (! isset($this->settings[$name])) {
            throw new ValueException(sprintf('Invalid flag name: "%s"', $name));
        }

        return $this->settings[$name];
    }

    /**
     * @return array<string, bool>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    protected static function isPowerOfTwo(int $flags): bool
    {
        return $flags > 0 && ($flags & ($flags - 1)) === 0;
    }
}
