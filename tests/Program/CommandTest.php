<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\Program;

use PHPUnit\Framework\Attributes\DataProvider;
use DouglasGreen\Utility\Data\ArgumentException;
use DouglasGreen\Utility\Program\Command;
use DouglasGreen\Utility\Program\CommandException;
use Exception;
use Iterator;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    public static function addArgProvider(): Iterator
    {
        yield ['simple', "'simple'"];
        yield ['with space', "'with space'"];
        yield ['with"quote', "'with\"quote'"];
        yield ["with'quote", "'with'\\''quote'"];
    }

    public static function addFlagProvider(): Iterator
    {
        yield ['-l', null, '-l'];
        yield ['--long', null, '--long'];
        yield ['-f', 'file.txt', "-f 'file.txt'"];
        yield ['--file', 'file.txt', "--file='file.txt'"];
    }

    public static function addSubcommandProvider(): Iterator
    {
        yield ['|', 'grep', ['pattern'], "| grep 'pattern'"];
        yield ['>', null, ['output.txt'], "> 'output.txt'"];
        yield ['>>', 'tee', ['log.txt'], ">> tee 'log.txt'"];
    }

    public static function errorCodeCommandProvider(): Iterator
    {
        yield 'File not found' => ['ls', ['non_existent_file'], 2];
        yield 'Permission denied' => ['cat', ['/etc/shadow'], 1];
        yield 'Command not found' => ['non_existent_command', [], 127];
        yield 'Invalid option' => ['ls', ['--invalid-option'], 2];
        yield 'Operation not permitted' => ['chmod', ['777', '/etc/passwd'], 1];
        yield 'Name or service not known' => ['ping', ['-c', '1', 'non_existent_host'], 2];
        yield 'No such process' => ['kill', ['-9', '999999'], 1];
        yield 'Always success' => ['true', [], 0];
        yield 'Always failure' => ['false', [], 1];
    }

    public static function invalidRedirectProvider(): Iterator
    {
        yield 'Invalid operator' => ['invalid', 'file.txt'];
        yield 'Empty operator' => ['', 'file.txt'];
        yield 'Empty source/target' => ['>', ''];
        yield 'Whitespace source/target' => ['>', '   '];
    }

    public static function validRedirectProvider(): Iterator
    {
        yield 'Output redirection' => [
            'echo Hello',
            '>',
            'output.txt',
            "echo Hello > 'output.txt'",
        ];
        yield 'Input redirection' => ['cat', '<', 'input.txt', "cat < 'input.txt'"];
        yield 'Append output' => ['echo World', '>>', 'log.txt', "echo World >> 'log.txt'"];
        yield 'Error redirection' => [
            'ls /nonexistent',
            '2>',
            'error.log',
            "ls /nonexistent 2> 'error.log'",
        ];
        yield 'Both stdout and stderr' => ['ls', '&>', 'all.log', "ls &> 'all.log'"];
        yield 'Pipe to another command' => ['echo Test', '|', 'grep T', "echo Test | 'grep T'"];
    }

    #[DataProvider('addArgProvider')]
    public function testAddArg(string $arg, string $expected): void
    {
        $command = new Command('echo');
        $command->addArg($arg);
        $this->assertSame('echo ' . $expected, (string) $command);
    }

    #[DataProvider('addFlagProvider')]
    public function testAddFlag(string $flag, ?string $argument, string $expected): void
    {
        $command = new Command('ls');
        $command->addFlag($flag, $argument);
        $this->assertSame('ls ' . $expected, (string) $command);
    }

    public function testAddFlagWithInvalidFlag(): void
    {
        $this->expectException(ArgumentException::class);
        $command = new Command('ls');
        $command->addFlag('invalid flag');
    }

    #[DataProvider('invalidRedirectProvider')]
    public function testAddRedirectWithInvalidOperators(
        string $redirectOperator,
        string $sourceOrTarget
    ): void {
        $command = new Command('echo');

        $this->expectException(ArgumentException::class);
        $command->addRedirect($redirectOperator, $sourceOrTarget);
    }

    #[DataProvider('validRedirectProvider')]
    public function testAddRedirectWithValidOperators(
        string $baseCommand,
        string $redirectOperator,
        string $sourceOrTarget,
        string $expectedCommand
    ): void {
        $command = new Command($baseCommand);
        $command->addRedirect($redirectOperator, $sourceOrTarget);

        $this->assertSame($expectedCommand, $command->buildCommand());
    }

    #[DataProvider('addSubcommandProvider')]
    public function testAddSubcommand(
        string $operator,
        ?string $subcommand,
        array $args,
        string $expected
    ): void {
        $command = new Command('ls');
        $command->addSubcommand($operator, $subcommand, $args);
        $this->assertSame('ls ' . $expected, (string) $command);
    }

    public function testAddSubcommandWithInvalidOperator(): void
    {
        $this->expectException(ArgumentException::class);
        $command = new Command('ls');
        $command->addSubcommand('invalid', 'grep');
    }

    public function testBuildCommand(): void
    {
        $command = new Command('ls');
        $command->addArg('-l')
            ->addFlag('--color', 'auto')
            ->addSubcommand('|', 'grep', ['pattern'])
            ->addSubcommand('>', null, ['output.txt']);

        $expected = "ls '-l' --color='auto' | grep 'pattern' > 'output.txt'";
        $this->assertSame($expected, $command->buildCommand());
    }

    public function testConstructor(): void
    {
        $command = new Command('ls');
        $this->assertInstanceOf(Command::class, $command);
    }

    public function testConstructorWithInvalidCommand(): void
    {
        $this->expectException(ArgumentException::class);
        new Command('invalid;command');
    }

    public function testRun(): void
    {
        $command = new Command('echo test');
        $output = $command->run();
        $this->assertSame(['test'], $output);
        $this->assertSame(0, $command->getReturnCode());
    }

    public function testRunAndPrint(): void
    {
        $command = new Command('echo test');
        $output = $command->runAndPrint();
        $this->assertSame('test', $output);
        $this->assertSame(0, $command->getReturnCode());
    }

    public function testRunAndPrintBinary(): void
    {
        $command = new Command('echo test');

        ob_start();
        $command->runAndPrintBinary();
        $output = ob_get_clean();

        $this->assertSame("test\n", $output);
        $this->assertSame(0, $command->getReturnCode());
    }

    #[DataProvider('errorCodeCommandProvider')]
    public function testRunAndPrintBinaryWithError(
        string $command,
        array $args,
        int $expectedErrorCode
    ): void {
        $commandObj = new Command($command);
        foreach ($args as $arg) {
            $commandObj->addArg($arg);
        }

        $commandObj->addRedirect('2>', '/dev/null');

        try {
            $commandObj->runAndPrintBinary();
        } catch (Exception) {
            // Catch any exceptions thrown by the run method
        }

        $this->assertSame($expectedErrorCode, $commandObj->getReturnCode());
    }

    #[DataProvider('errorCodeCommandProvider')]
    public function testRunAndPrintWithError(
        string $command,
        array $args,
        int $expectedErrorCode
    ): void {
        $commandObj = new Command($command);
        foreach ($args as $arg) {
            $commandObj->addArg($arg);
        }

        $commandObj->addRedirect('2>', '/dev/null');

        try {
            $commandObj->runAndPrint();
        } catch (Exception) {
            // Catch any exceptions thrown by the run method
        }

        $this->assertSame($expectedErrorCode, $commandObj->getReturnCode());
    }

    #[DataProvider('errorCodeCommandProvider')]
    public function testRunWithError(string $command, array $args, int $expectedErrorCode): void
    {
        $commandObj = new Command($command);
        foreach ($args as $arg) {
            $commandObj->addArg($arg);
        }

        $commandObj->addRedirect('2>', '/dev/null');

        try {
            $commandObj->run();
        } catch (Exception) {
            // Catch any exceptions thrown by the run method
        }

        $this->assertSame($expectedErrorCode, $commandObj->getReturnCode());
    }

    public function testShellExec(): void
    {
        $command = new Command('echo test');
        $output = $command->shellExec();
        $this->assertSame("test\n", $output);
    }

    public function testShellExecWithAllowEmptyOutput(): void
    {
        $command = new Command('true');
        $output = $command->shellExec(Command::ALLOW_EMPTY_OUTPUT);
        $this->assertNull($output);
    }

    public function testShellExecWithEmptyOutput(): void
    {
        $command = new Command('true');
        $this->expectException(CommandException::class);
        $command->shellExec(0);
    }
}
