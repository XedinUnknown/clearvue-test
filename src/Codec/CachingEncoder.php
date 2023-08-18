<?php

declare(strict_types=1);

namespace Clearvue\Test1\Codec;

use Clearvue\Test1\CachingStream;
use Psr\Http\Message\StreamInterface;

/**
 * An encoder that creates cached streams.
 *
 * @psalm-immutable
 * @template Subject of iterable
 * @implements StreamingEncoderInterface<Subject>
 */
class CachingEncoder implements StreamingEncoderInterface
{
    public function __construct(protected StreamingEncoderInterface $encoder)
    {
    }

    /**
     * @inheritDoc
     */
    public function encode(iterable $data): StreamInterface
    {
        $stream = $this->encoder->encode($data);
        $cache = new CachingStream($stream);

        return $cache;
    }

    /**
     * @inheritDoc
     */
    public function getMimeType(): string
    {
        return $this->encoder->getMimeType();
    }
}
