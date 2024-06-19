<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Program;

use DouglasGreen\Utility\Regex\Regex;
use DouglasGreen\Utility\Data\FlagChecker;
use DouglasGreen\Utility\Data\FlagHandler;

/**
 * Command utility class to throw exceptions when basic operations fail.
 */
class Command implements FlagHandler
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

    protected int $returnCode;

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
     *
     * @throws CommandException
     */
    public function __construct(
        protected readonly string $command
    ) {
        $sep = preg_quote(PATH_SEPARATOR, '/');
        if (Regex::hasMatch('/[^\w\s' . $sep . '.-]/', $this->command)) {
            throw new CommandException(sprintf('Invalid command: "%s"', $this->command));
        }
    }

    public function addArg(string $arg): self
    {
        $arg = trim($arg);
        if ($arg === '') {
            return $this;
        }

        $this->args[] = escapeshellarg($arg);
        return $this;
    }

    public function buildCommand(): string
    {
        $command = $this->command;
        if ($this->args !== []) {
            $command .= ' ' . implode(' ', $this->args);
        }

        return $command;
    }

    /**
     * Wrapper for exec.
     *
     * @return list<string>
     * @throws CommandException
     */
    public function exec(): array
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
    public function execAndPrint(): string
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
    public function execAndPrintBinary(): void
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

    public function getReturnCode(): ?int
    {
        return $this->returnCode;
    }

    /**
     * Wrapper for shell_exec.
     *
     * @throws CommandException
     */
    public function shellExec(int $flags): ?string
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
}
