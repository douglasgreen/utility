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
 *     'isFlagOne' => 1,
 *     'isFlagTwo' => 2,
 *     'isFlagFour' => 4,
 * ];
 * $value = 5;
 * $flag = new Flag($flags, $value);
 * print_r($flag->isFlagTwo);
 */
class FlagChecker
{
    /**
     * @var array<string, bool>
     */
    protected array $settings = [];

    /**
     * @param array<string, int> $flags
     * @throws ValueException
     */
    public function __construct(array $flags, int $value)
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
        foreach ($flags as $name => $flag) {
            $this->settings[$name] = (bool) ($value & $flag);
        }

        // Check for any invalid flags in the value
        $validFlagsSum = array_sum(array_keys($uniqueValues));
        if (($value & ~$validFlagsSum) !== 0) {
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

    protected function isPowerOfTwo(int $value): bool
    {
        return $value > 0 && ($value & ($value - 1)) === 0;
    }
}
