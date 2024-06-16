<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Program;

use DouglasGreen\Utility\Regex\Regex;

/**
 * Command utility class to throw exceptions when basic operations fail.
 */
class Command
{
    public const ALLOW_EMPTY_OUTPUT = 1;

    /**
     * @var list<string>
     */
    protected array $args;

    /**
     * @var list<string>
     */
    protected array $output;

    protected int $resultCode;

    /**
     * @throws CommandException
     */
    public function __construct(
        protected string $command,
        protected int $flags = 0
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

    /**
     * Wrapper for exec.
     *
     * @return list<string>
     * @throws CommandException
     */
    public function exec(): array
    {
        $result = exec($this->buildCommand(), $output, $resultCode);
        if ($result === false) {
            throw new CommandException(
                sprintf('Command "%s" failed with error code "%s"', $this->command, $resultCode),
            );
        }

        $this->output = $output;
        $this->resultCode = $resultCode;

        return $this->output;
    }

    /**
     * Wrapper for system.
     *
     * @throws CommandException
     */
    public function execAndPrint(): string
    {
        $result = system($this->buildCommand(), $this->resultCode);
        if ($result === false) {
            throw new CommandException(
                sprintf(
                    'Command "%s" failed with error code "%s"',
                    $this->command,
                    $this->resultCode,
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
        $result = passthru($this->buildCommand(), $this->resultCode);
        if ($result === false) {
            throw new CommandException(
                sprintf(
                    'Command "%s" failed with error code "%s"',
                    $this->command,
                    $this->resultCode,
                ),
            );
        }
    }

    public function getResultCode(): ?int
    {
        return $this->resultCode;
    }

    /**
     * Wrapper for shell_exec.
     *
     * @throws CommandException
     */
    public function shellExec(): ?string
    {
        $allowEmptyOutput = (bool) ($this->flags & self::ALLOW_EMPTY_OUTPUT);
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

    protected function buildCommand(): string
    {
        $command = $this->command;
        if ($this->args !== []) {
            $command .= ' ' . implode(' ', $this->args);
        }

        return $command;
    }
}
