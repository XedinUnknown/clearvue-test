<?php

declare(strict_types=1);

namespace Clearvue\Test1\Handlers;

use Clearvue\Test1\Codec\StreamingEncoderInterface;
use Clearvue\Test1\Commands\City\ListCommand;
use Clearvue\Test1\Transform\FormatInterface;
use Clearvue\Test1\Transform\SerializerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Lists API objects.
 *
 * @template Item of object
 */
class ListHandler implements HandlerInterface
{
    /**
     * @phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification)
     * @param ListCommand<array, Item> $listCommand
     * @phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification)
     * @param StreamingEncoderInterface<iterable> $encoder
     * @param SerializerInterface<array<string, scalar>, Item> $serializer
     * @param FormatInterface<iterable<scalar|iterable<scalar>>, Item> $responseFormat
     */
    public function __construct(
        protected ListCommand $listCommand,
        protected StreamingEncoderInterface $encoder,
        protected SerializerInterface $serializer,
        protected FormatInterface $responseFormat,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $serializer = $this->serializer;
        $encoder = $this->encoder;
        $selectedObjects = $this->listCommand->listAll();
        $responseData = $this->responseFormat->formatList($selectedObjects, $serializer);
        $contentType = $encoder->getMimeType();
        $body = $encoder->encode($responseData);
        $response = $response->withAddedHeader('Content-Type', $contentType);
        $response = $response->withBody($body);

        return $response;
    }
}
