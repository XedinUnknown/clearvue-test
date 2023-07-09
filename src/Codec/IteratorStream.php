<?php

declare(strict_types=1);

namespace Clearvue\Test1\Codec;

use Exception;
use Iterator;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class IteratorStream implements StreamInterface
{
    protected int $index = 0;
    protected int $position = 0;
    protected string $data = '';
    protected ?Iterator $iterator = null;

    /**
     * @param Iterator $iterator
     */
    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        try {
            return $this->getContents();
        } catch (Exception) {
            return 'ERROR';
        }
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        $this->iterator = null;

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function tell()
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     *
     * @throws RuntimeException If problem determining state.
     */
    public function eof()
    {
        if ($this->iterator === null) {
            throw new RuntimeException('This stream is detached');
        }

        return $this->index !== 0
            && !$this->iterator->valid();
    }

    /**
     * @inheritDoc
     */
    public function isSeekable()
    {
        return false;
    }

    /**
     * @inheritDoc
     *
     * @return never
     */
    public function seek(int $offset, int $whence = SEEK_SET)
    {
        throw new RuntimeException('This stream is not seekable');
    }

    /**
     * @inheritDoc
     *
     * @return never
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * @inheritDoc
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function write(string $string)
    {
        throw new RuntimeException('This stream is not writable');
    }

    /**
     * @inheritDoc
     */
    public function isReadable()
    {
        return $this->iterator !== null;
    }

    /**
     * @inheritDoc
     */
    public function read(int $length)
    {
        $iterator = $this->iterator;
        if ($iterator === null) {
            throw new RuntimeException('This stream is detached');
        }

        // Ensure enough content
        do {
            // Advance
            if ($this->index === 0) {
                $iterator->rewind();
            } else {
                $iterator->next();
            }
            $this->index++;

            // Stop if end
            if (!$iterator->valid()) {
                break;
            }

            // Add content
            $data = (string) $iterator->current();
            $this->data .= $data;
            $this->position += strlen($data);
        } while (strlen($this->data) < $length);

        // Get piece of content
        $output = substr($this->data, 0, $length);
        $this->data = substr($this->data, strlen($output));

        return $output;
    }

    /**
     * @inheritDoc
     */
    public function getContents()
    {
        $contents = '';
        while (!$this->eof()) {
            $contents .= $this->read(1000);
        }

        return $contents;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata(?string $key = null)
    {
        return ($key === null) ? [] : null;
    }
}
