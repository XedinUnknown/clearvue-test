<?php

declare(strict_types=1);

namespace Clearvue\Test1\Handlers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Can handle a route.
 *
 * @psalm-type RequestHandler = callable(RequestInterface, ResponseInterface): ResponseInterface
 */
interface HandlerInterface
{
    /**
     * Handles a route.
     *
     * @see https://www.slimframework.com/docs/v4/objects/routing.html
     *
     * @param RequestInterface $request The incoming request.
     * @param ResponseInterface $response The outgoing response, up to now.
     *
     * @return ResponseInterface The outgoing response, after this handler.
     *
     * @throws RuntimeException If problem handling.
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response): ResponseInterface;
}
