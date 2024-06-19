<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Program;

/**
 * Process class to throw exceptions when basic operations fail.
 *
 * It's called "simple" because popen is the reduced, one-way version of proc_open, which is wrapped
 * by the Process class.
 *
 * The functions in this class depend on popen.
 *
 * The basic pattern of operations is:
 * 1. Open a pipe to a process for reading or writing.
 * 2. Read or write occurs.
 * 3. Close pipe.
 *
 * @todo Write the Process class.
 */
class SimpleProcess
{
    /**
     * @var ?resource
     */
    protected $stream;

    public function __construct(
        protected readonly string $command,
        protected readonly string $mode = 'r',
    ) {}

    /**
     * Substitute for pclose.
     *
     * Returns null if PHP has been compiled with --enable-sigchild.
     *
     * @throws CommandException
     */
    public function close(): ?int
    {
        if ($this->stream === null) {
            return null;
        }

        $result = pclose($this->stream);
        $this->stream = null;

        if ($this->sigchildEnabled()) {
            return null;
        }

        if ($result === -1) {
            throw new CommandException(sprintf('Unable to close pipe to "%s"', $this->command));
        }

        return $result;
    }

    /**
     * Substitute for fgets.
     *
     * @param ?int<0, max> $length
     * @throws CommandException
     */
    public function getLine(?int $length = null): ?string
    {
        if ($this->stream === null) {
            throw new CommandException('Stream not open');
        }

        $buffer = fgets($this->stream, $length);

        // Distinguish between end-of-data false and error false.
        if ($buffer === false) {
            if (! feof($this->stream)) {
                throw new CommandException(
                    sprintf('Unable to get line from pipe to "%s"', $this->command),
                );
            }

            return null;
        }

        return $buffer;
    }

    /**
     * Substitute for popen.
     *
     * @throws CommandException
     */
    public function open(): void
    {
        $stream = popen($this->command, $this->mode);
        if ($stream === false) {
            throw new CommandException(sprintf('Unable to open pipe to "%s"', $this->command));
        }

        $this->stream = $stream;
    }

    /**
     * Substitute for fread.
     *
     * @param int<1, max> $length
     * @throws CommandException
     */
    public function read(int $length): string
    {
        if ($this->stream === null) {
            throw new CommandException('Stream not open');
        }

        $result = fread($this->stream, $length);
        if ($result === false) {
            throw new CommandException(
                sprintf('Unable to read string from pipe to "%s"', $this->command),
            );
        }

        return $result;
    }

    /**
     * Substitute for fwrite and its alias fputs.
     *
     * @param ?int<0, max> $length
     * @throws CommandException
     */
    public function write(string $data, ?int $length = null): int
    {
        if ($this->stream === null) {
            throw new CommandException('Stream not open');
        }

        $result = fwrite($this->stream, $data, $length);
        if ($result === false) {
            throw new CommandException(
                sprintf('Unable to write string to pipe to "%s"', $this->command),
            );
        }

        return $result;
    }

    /**
     * @throws ProgramException
     */
    protected function sigchildEnabled(): bool
    {
        ob_start();
        phpinfo(INFO_GENERAL);
        $info = ob_get_clean();
        if ($info === false) {
            throw new ProgramException('Unable to get PHP info');
        }

        return str_contains($info, '--enable-sigchild');
    }
}
