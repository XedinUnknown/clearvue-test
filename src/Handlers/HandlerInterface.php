<?php

declare(strict_types=1);

namespace Clearvue\Test1\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 * Can handle a route.
 *
 * @psalm-type RequestHandler = callable(ServerRequestInterface, ResponseInterface): ResponseInterface
 */
interface HandlerInterface
{
    /**
     * Handles a route.
     *
     * @see https://www.slimframework.com/docs/v4/objects/routing.html
     *
     * @param ServerRequestInterface $request The incoming request.
     * @param ResponseInterface $response The outgoing response, up to now.
     *
     * @return ResponseInterface The outgoing response, after this handler.
     *
     * @throws RuntimeException If problem handling.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
