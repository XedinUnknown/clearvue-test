<?php

declare(strict_types=1);

namespace Clearvue\Test1;

use Exception;
use OutOfBoundsException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use UnexpectedValueException;

use function PHPUnit\Framework\isReadable;

/**
 * A stream that caches contents of another stream incrementally.
 *
 * Each {@link self::seek()} that is beyond what is already cached will trigger
 * more data from the wrapped stream to be read, up to that position, and no further.
 *
 * Each {@link self::read()} will {@link self::seek()} if more cache is needed first.
 *
 * Because this stream works with cached data, it can be {@link self::rewind()}
 * and otherwise operated even if the underlying stream is finished, one-time-use,
 * or is otherwise unusable after the first read.
 */
class CachingStream implements StreamInterface
{
    protected ?StreamInterface $stream;
    protected int $position = 0;
    protected ?string $data = null;

    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
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
        $this->data = null;

        if ($this->stream !== null) {
            $this->stream->close();
        }
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        $this->stream = null;

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        $stream = $this->stream;
        $childSize = $stream->getSize();

        if ($childSize !== null) {
            return $childSize;
        }

        if ($stream->eof()) {
            return $this->position;
        }

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
        $stream = $this->stream;

        return ($stream === null || $stream->eof())
            && ($this->position === (strlen((string) $this->data) - 1));
    }

    /**
     * @inheritDoc
     *
     * @see isReadable()
     *
     * Can seek if readable.
     */
    public function isSeekable()
    {
        return $this->isReadable();
    }

    /**
     * @inheritDoc
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        switch ($whence) {
            case SEEK_CUR:
                $offset = $this->position + $offset;
                break;

            case SEEK_END:
                $size = $this->getSize();
                if ($size === null) {
                    throw new UnexpectedValueException(
                        sprintf('Unable to determine end position: underlying stream size unknown')
                    );
                }

                $end = $size - 1;
                $offset = $this->position + $end;
                break;

            case SEEK_SET:
            default:
                // Nothing to do: exact position specified
        }

        // If requested piece is not yet cached, attempt to read until offset
        $stream = $this->stream;
        if ($offset > strlen((string) $this->data) - 1 && $stream !== null && !$stream->eof()) {
            if (! $stream instanceof StreamInterface) {
                throw new UnexpectedValueException('This stream is detached');
            }

            // Prime data if nothing read yet
            $this->data = $this->data ?? '';

            $toRead = $offset - $this->position;
            $this->data .= $stream->read($toRead);
        }

        // If still insufficient, halt
        if ($offset > strlen((string) $this->data)) {
            throw new OutOfBoundsException(
                sprintf('Offset %1$d is greater than stream content length %2$d', $offset, strlen((string) $this->data))
            );
        }

        // Data already cached; simply fast-forward to the offset
        $this->position = $offset;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * @inheritDoc
     */
    public function isWritable(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function write(string $string): int
    {
        throw new RuntimeException('This stream is not writable');
    }

    /**
     * @inheritDoc
     *
     * Can read if there's a readable stream still attached that we can fill cache from.
     * Alternatively, even if detached, can still read if there's cache already.
     */
    public function isReadable(): bool
    {
        return ($this->stream !== null && $this->stream->isReadable())
            || (is_string($this->data));
    }

    /**
     * @inheritDoc
     */
    public function read(int $length): string
    {
        $readStart = $this->position;
        $readEnd = $readStart + $length;

        // If we've got to read past end of cache, attempt to seek to that point to cache that piece
        if ($readEnd > strlen((string) $this->data) - 1) {
            try {
                $this->seek($readEnd);
            } catch (OutOfBoundsException) {
                // This is fine: reading can return less than requested
            }
        }

        $output = substr((string) $this->data, $readStart, $length);
        $this->position = $readStart + strlen($output) - 1;

        return $output;
    }

    /**
     * @inheritDoc
     */
    public function getContents(): string
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
