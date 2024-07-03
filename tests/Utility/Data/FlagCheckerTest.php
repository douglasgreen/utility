<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\Data\FlagChecker;

use DouglasGreen\Utility\Data\FlagChecker;
use DouglasGreen\Utility\Data\ValueException;
use PHPUnit\Framework\TestCase;

class FlagCheckerTest extends TestCase
{
    public function testGetInvalidFlagName(): void
    {
        $this->expectException(ValueException::class);
        $flagNames = [
            'recursive' => 1,
            'depthFirst' => 2,
        ];
        $flags = 1;

        $flagChecker = new FlagChecker($flagNames, $flags);
        $flagChecker->get('nonExistentFlag');
    }

    public function testGetSettings(): void
    {
        $flagNames = [
            'recursive' => 1,
            'depthFirst' => 2,
        ];
        $flags = 3;

        $flagChecker = new FlagChecker($flagNames, $flags);
        $settings = $flagChecker->getSettings();

        $this->assertCount(2, $settings);
        $this->assertArrayHasKey('recursive', $settings);
        $this->assertArrayHasKey('depthFirst', $settings);
        $this->assertTrue($settings['recursive']);
        $this->assertTrue($settings['depthFirst']);
    }

    public function testInvalidFlagsInValue(): void
    {
        $this->expectException(ValueException::class);
        $flagNames = [
            'recursive' => 1,
            'depthFirst' => 2,
        ];
        $flags = 4; // Invalid flag value

        new FlagChecker($flagNames, $flags);
    }

    public function testInvalidFlagValue(): void
    {
        $this->expectException(ValueException::class);
        $flagNames = [
            'recursive' => 1,
            'depthFirst' => 2,
            'invalidFlag' => 3, // Not a power of two
        ];
        $flags = 3;

        new FlagChecker($flagNames, $flags);
    }

    public function testNonUniqueFlags(): void
    {
        $this->expectException(ValueException::class);
        $flagNames = [
            'recursive' => 1,
            'depthFirst' => 1, // Not unique
        ];
        $flags = 1;

        new FlagChecker($flagNames, $flags);
    }

    public function testValidFlags(): void
    {
        $flagNames = [
            'recursive' => 1,
            'depthFirst' => 2,
        ];
        $flags = 3; // Both flags are set

        $flagChecker = new FlagChecker($flagNames, $flags);

        $this->assertTrue($flagChecker->get('recursive'));
        $this->assertTrue($flagChecker->get('depthFirst'));
    }
}
