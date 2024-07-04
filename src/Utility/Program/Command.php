<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Program;

use DouglasGreen\Utility\Data\ArgumentException;
use DouglasGreen\Utility\Data\FlagChecker;
use DouglasGreen\Utility\Data\FlagHandler;
use DouglasGreen\Utility\Regex\Regex;
use Stringable;

/**
 * Command utility class to throw exceptions when basic operations fail.
 *
 * This class provides a structured way to build and execute shell commands with proper validation
 * and error handling mechanisms.
 */
class Command implements FlagHandler, Stringable
{
    public const ALLOW_EMPTY_OUTPUT = 1;

    /**
     * @var list<string>
     */
    protected array $args = [];

    /**
     * @var list<string>
     */
    protected array $output;

    /**
     * @var list<array{op: string, command: ?string, args: list<string>}>
     */
    protected array $subcommands = [];

    protected ?int $returnCode = null;

    public static function getFlagChecker(int $flags): FlagChecker
    {
        $flagNames = [
            'allowEmptyOutput' => self::ALLOW_EMPTY_OUTPUT,
        ];
        return new FlagChecker($flagNames, $flags);
    }

    /**
     * Set a command to execute.
     *
     * The command is limited to words, spaces, dots, and hyphens. Any arguments with different characters
     * than those will need to be added using addArg so they can be properly escaped.
     */
    public function __construct(
        protected readonly string $command
    ) {
        self::validateCommand($this->command);
    }

    public function __toString(): string
    {
        return $this->buildCommand();
    }

    /**
     * Adds an argument to the command.
     *
     * This method trims the input argument and escapes it for shell usage. If the argument is
     * empty after trimming, it will not be added.
     *
     * @param string $arg The argument to add to the command.
     */
    public function addArg(string $arg): self
    {
        $arg = trim($arg);
        if ($arg === '') {
            return $this;
        }

        $this->args[] = escapeshellarg($arg);
        return $this;
    }

    /**
     * Adds a flag to the command, optionally with an argument.
     *
     * This method validates the flag format and adds it to the command. If a flag argument is
     * provided, it is properly separated and escaped.
     *
     * @param string $flag The flag to add (e.g., '-f' or '--flag').
     * @param string|null $flagArgument Optional argument for the flag.
     * @throws ArgumentException If the flag format is invalid.
     */
    public function addFlag(string $flag, ?string $flagArgument = null): self
    {
        $flag = trim($flag);
        $match = Regex::match('/^(--?)\w+(-\w+)*$/', $flag);
        if ($match === []) {
            throw new ArgumentException(sprintf('Invalid flag: "%s"', $flag));
        }

        $separator = $match[1] === '--' ? '=' : ' ';
        $arg = $flag;
        if ($flagArgument !== null) {
            $arg .= $separator . escapeshellarg($flagArgument);
        }

        $this->args[] = $arg;
        return $this;
    }

    /**
     * Adds a redirection to the command.
     *
     * @param string $redirectionOperator The redirection operator (e.g., '>', '>>', '<', '2>', etc.)
     * @param string $sourceOrTarget The source or target for the redirection
     * @throws ArgumentException If the redirection operator is invalid or the source/target is empty
     */
    public function addRedirect(string $redirectionOperator, string $sourceOrTarget): self
    {
        if (trim($sourceOrTarget) === '') {
            throw new ArgumentException('Source or target for redirection cannot be empty');
        }

        self::validateRedirectionOperator($redirectionOperator);

        $this->subcommands[] = [
            'op' => $redirectionOperator,
            'command' => null,
            'args' => [$sourceOrTarget],
        ];

        return $this;
    }

    /**
     * Adds a subcommand to the command with a redirection operator.
     *
     * This method validates the redirection operator and the subcommand (if provided). It then
     * adds the subcommand with its arguments to the command structure.
     *
     * @param string $redirectionOperator The redirection operator (e.g., '|', '>', '>>', '<').
     * @param string|null $subcommand The subcommand to add, or null if not applicable.
     * @param list<string> $args An array of arguments for the subcommand.
     * @throws ArgumentException If both subcommand and args are empty, or if the redirection operator is invalid.
     */
    public function addSubcommand(
        string $redirectionOperator,
        ?string $subcommand = null,
        array $args = [],
    ): self {
        if ($subcommand === null && $args === []) {
            throw new ArgumentException('Subcommand and args are both empty');
        }

        self::validateRedirectionOperator($redirectionOperator);

        if ($subcommand !== null) {
            self::validateCommand($subcommand);
        }

        // Add the subcommand if the operator is valid
        $this->subcommands[] = [
            'op' => $redirectionOperator,
            'command' => $subcommand,
            'args' => $args,
        ];
        return $this;
    }

    public function buildCommand(): string
    {
        $command = $this->command;
        if ($this->args !== []) {
            $command .= ' ' . implode(' ', $this->args);
        }

        foreach ($this->subcommands as $subcommand) {
            $command .= ' ' . $subcommand['op'];
            if ($subcommand['command'] !== null) {
                $command .= ' ' . $subcommand['command'];
            }

            foreach ($subcommand['args'] as $arg) {
                $command .= ' ' . escapeshellarg($arg);
            }
        }

        return $command;
    }

    public function getReturnCode(): ?int
    {
        return $this->returnCode;
    }

    /**
     * Wrapper for exec.
     *
     * @return list<string>
     * @throws CommandException
     */
    public function run(): array
    {
        $result = exec($this->buildCommand(), $output, $returnCode);
        if ($result === false) {
            throw new CommandException(
                sprintf('Command "%s" failed with error code "%s"', $this->command, $returnCode),
            );
        }

        $this->output = $output;
        $this->returnCode = $returnCode;

        return $this->output;
    }

    /**
     * Wrapper for system.
     *
     * @throws CommandException
     */
    public function runAndPrint(): string
    {
        $result = system($this->buildCommand(), $this->returnCode);
        if ($result === false) {
            throw new CommandException(
                sprintf(
                    'Command "%s" failed with error code "%s"',
                    $this->command,
                    $this->returnCode,
                ),
            );
        }

        return $result;
    }

    /**
     * Wrapper for passthru.
     *
     * @throws CommandException
     */
    public function runAndPrintBinary(): void
    {
        $result = passthru($this->buildCommand(), $this->returnCode);
        if ($result === false) {
            throw new CommandException(
                sprintf(
                    'Command "%s" failed with error code "%s"',
                    $this->command,
                    $this->returnCode,
                ),
            );
        }
    }

    /**
     * Wrapper for shell_exec.
     *
     * @throws CommandException
     */
    public function shellExec(int $flags = 0): ?string
    {
        $flagChecker = static::getFlagChecker($flags);

        $allowEmptyOutput = $flagChecker->get('allowEmptyOutput');
        $result = shell_exec($this->buildCommand());
        if ($result === false) {
            throw new CommandException(
                sprintf('Command "%s" unable to establish pipe', $this->command),
            );
        }

        if ($result === null && ! $allowEmptyOutput) {
            throw new CommandException(
                sprintf('Command "%s" failed to produce output', $this->command),
            );
        }

        return $result;
    }

    /**
     * Validates a simple command string to ensure it contains only valid characters.
     *
     * This function checks if the provided command string is valid for execution on both Windows
     * and Linux systems. A valid command is a path to a file with possible switches that do not
     * require escaping due to having special characters.
     *
     * The regular expression used in the validation allows the following characters:
     * - \w: Word characters (letters, digits, and underscores)
     * - \s: Whitespace characters (spaces, tabs)
     * - \/: Forward slashes (for Linux paths)
     * - \\: Backslashes (for Windows paths)
     * - \:: Colons (for drive letters in Windows paths)
     * - \.: Dots (for file extensions)
     * - \-: Hyphens (common in file names and switches)
     * - \_: Underscores (common in file names and switches)
     *
     * If the command string is empty or contains invalid characters, an ArgumentException is
     * thrown.
     *
     * @param string $command The command string to validate.
     * @throws ArgumentException If the command string is invalid.
     */
    protected static function validateCommand(string $command): void
    {
        // Define a regular expression that matches valid characters in commands
        $pattern = '/^[\w\s\/\\\:\.\-\_]+$/';

        // Check if the command is empty or contains invalid characters
        if ($command === '' || ! Regex::hasMatch($pattern, $command)) {
            throw new ArgumentException(sprintf('Invalid command: "%s"', $command));
        }
    }

    /**
     * @throws ArgumentException
     */
    protected static function validateRedirectionOperator(string $redirectionOperator): void
    {
        // Define a list of valid redirection operators
        $validOperators = ['|', '>', '>>', '<', '2>', '2>>', '&>', '&>>', '>&', '<&'];

        // Check if the provided operator is valid
        if (! in_array($redirectionOperator, $validOperators, true)) {
            throw new ArgumentException(
                sprintf('Invalid redirection operator: "%s"', $redirectionOperator),
            );
        }
    }
}
