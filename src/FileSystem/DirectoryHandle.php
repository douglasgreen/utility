<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

/**
 * Directory utility class to throw exceptions when basic operations fail.
 *
 * Manages directory handles opened by opendir.
 */
class DirectoryHandle
{
    /**
     * @var resource
     */
    protected $handle;

    protected readonly string $directory;

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

    /**
     * Substitute for readdir.
     *
     * @throws DirectoryException
     */
    public function read(): string
    {
        $result = readdir($this->handle);
        if ($result === false) {
            throw new DirectoryException(
                sprintf('Unable to read directory handle: "%s"', $this->directory),
            );
        }

        return $result;
    }

    /**
     * Substitute for rewinddir.
     */
    public function rewind(): self
    {
        rewinddir($this->handle);

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
