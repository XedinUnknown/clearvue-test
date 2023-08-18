<?php

declare(strict_types=1);

namespace Clearvue\Test1\Handlers;

use Clearvue\Test1\Codec\StreamingEncoderInterface;
use Clearvue\Test1\Commands\GetCommand;
use Clearvue\Test1\Transform\FormatInterface;
use Clearvue\Test1\Transform\SerializerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Retrieves an API object.
 *
 * @template Item of object
 */
class GetHandler implements HandlerInterface
{
    /**
     * @phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification)
     * @param GetCommand<array, Item> $getCommand
     * @phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification)
     * @param StreamingEncoderInterface<iterable> $encoder
     * @param SerializerInterface<array<string, scalar>, Item> $serializer
     * @param FormatInterface<iterable<scalar|iterable<scalar>>, Item> $responseFormat
     */
    public function __construct(
        protected GetCommand $getCommand,
        protected StreamingEncoderInterface $encoder,
        protected SerializerInterface $serializer,
        protected FormatInterface $responseFormat,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $serializer = $this->serializer;
        $encoder = $this->encoder;
        $primaryKey = intval($request->getAttribute('id'));

        $retrievedObject = $this->getCommand->get($primaryKey);
        $responseData = $this->responseFormat->format($retrievedObject, $serializer);
        $contentType = $encoder->getMimeType();
        $body = $encoder->encode($responseData);
        $response = $response->withAddedHeader('Content-Type', $contentType);
        $response = $response->withBody($body);

        return $response;
    }
}
