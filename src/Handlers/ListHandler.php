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
 * @template T
 */
class ListHandler implements HandlerInterface
{
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
