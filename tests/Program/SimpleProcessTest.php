<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\Program;

use DouglasGreen\Utility\Program\CommandException;
use DouglasGreen\Utility\Program\SimpleProcess;
use PHPUnit\Framework\TestCase;

class SimpleProcessTest extends TestCase
{
    public function testConstructorAndToString(): void
    {
        $command = 'echo "Hello, World!"';
        $process = new SimpleProcess($command);

        $this->assertSame($command, (string) $process);
    }

    public function testInvalidCommand(): void
    {
        $process = new SimpleProcess('invalid_command_that_does_not_exist 2>&1');

        // This should not throw an exception
        $process->open();

        // Read the error message
        $errorOutput = $process->getLine();

        // Close the stream
        $returnCode = $process->close();

        // Assert that we got an error message
        $this->assertNotNull($errorOutput);
        $this->assertStringContainsString('not found', $errorOutput);
        $this->assertSame($returnCode, 127);
    }

    public function testOpenAndClose(): void
    {
        $process = new SimpleProcess('echo "Test"');

        $process->open();

        // Read the output before closing
        $output = $process->getLine();
        $this->assertSame("Test\n", $output);

        $result = $process->close();
        $this->assertNotNull($result);

        // Test reopening
        $process->open();
        $output = $process->getLine();
        $this->assertSame("Test\n", $output);

        $result = $process->close();
        $this->assertNotNull($result);
    }

    public function testReadingFromClosedStream(): void
    {
        $this->expectException(CommandException::class);
        $process = new SimpleProcess('echo "Test"');
        $process->getLine();
    }

    public function testReadingFromProcess(): void
    {
        $process = new SimpleProcess('echo "Line 1\nLine 2\nLine 3"');
        $process->open();

        $this->assertSame("Line 1\n", $process->getLine());
        $this->assertSame("Line 2\n", $process->getLine());
        $this->assertSame('Line 3', $process->read(6));

        $process->close();
    }

    public function testSigchildEnabled(): void
    {
        $result = SimpleProcess::sigchildEnabled();
        $this->assertIsBool($result);
    }

    public function testWritingToClosedStream(): void
    {
        $this->expectException(CommandException::class);
        $process = new SimpleProcess('cat', 'w');
        $process->write('Test');
    }

    public function testWritingToProcess(): void
    {
        $process = new SimpleProcess('cat', 'w');
        $process->open();

        $bytesWritten = $process->write('Hello, World!');
        $this->assertSame(13, $bytesWritten);

        $process->close();
    }
}
