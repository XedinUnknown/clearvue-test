<?php

declare(strict_types=1);

namespace Clearvue\Test1\Codec;

use Psr\Http\Message\StreamInterface;
use Violet\StreamingJsonEncoder\BufferJsonEncoder;

/**
 * Can encode data into the JSON format.
 */
class JsonStreamingEncoder implements StreamingEncoderInterface
{
    /** The MIME type of encoded content */
    protected const MIME_TYPE = 'application/json';
    protected bool $isPrettyPrint;

    /**
     * @param bool $isPrettyPrint If true, the resulting JSON will be broken down into lines and indented.
     */
    public function __construct(bool $isPrettyPrint)
    {
        $this->isPrettyPrint = $isPrettyPrint;
    }

    /**
     * @inheritDoc
     */
    public function encode(iterable $data): StreamInterface
    {
        $encoder = new BufferJsonEncoder($data);
        if ($this->isPrettyPrint) {
            $encoder->setOptions(JSON_PRETTY_PRINT);
        }

        $stream = new IteratorStream($encoder);

        return $stream;
    }

    /**
     * @inheritDoc
     */
    public function getMimeType(): string
    {
        return self::MIME_TYPE;
    }
}
