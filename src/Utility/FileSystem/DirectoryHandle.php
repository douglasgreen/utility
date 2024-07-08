<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use Stringable;

/**
 * Directory utility class to throw exceptions when basic operations fail.
 *
 * Manages directory handles opened by opendir.
 */
class DirectoryHandle implements Stringable
{
    /**
     * @var resource
     */
    protected $handle;

    protected readonly string $directory;

    protected bool $atEnd = false;

    /**
     * Calls opendir.
     *
     * @param ?resource $context
     */
    public function __construct(
        ?string $directory = null,
        protected $context = null
    ) {
        if ($directory === null) {
            $directory = DirUtil::getCurrent();
        }

        $this->directory = $directory;
        $this->handle = $this->open();
    }

    /**
     * Calls opendir.
     */
    public function __destruct()
    {
        closedir($this->handle);
    }

    public function __toString(): string
    {
        return $this->directory;
    }

    /**
     * Substitute for readdir.
     *
     * @throws DirectoryException
     */
    public function read(): ?string
    {
        $result = readdir($this->handle);
        if ($result === false) {
            $error = error_get_last();
            if ($error !== null) {
                throw new DirectoryException(
                    sprintf(
                        'Error "%s" reading directory handle "%s"',
                        $error['message'],
                        $this->directory
                    )
                );
            }

            if ($this->atEnd) {
                throw new DirectoryException(
                    sprintf('Read past end of directory: "%s"', $this->directory)
                );
            }

            $this->atEnd = true;
            return null;

        }

        return $result;
    }

    /**
     * Substitute for rewinddir.
     */
    public function rewind(): self
    {
        rewinddir($this->handle);
        $this->atEnd = false;

        return $this;
    }

    /**
     * Substitute for opendir.
     *
     * @return resource
     * @throws DirectoryException
     */
    protected function open()
    {
        $result = opendir($this->directory, $this->context);
        if ($result === false) {
            throw new DirectoryException(
                sprintf('Unable to open directory handle: "%s"', $this->directory),
            );
        }

        return $result;
    }
}
