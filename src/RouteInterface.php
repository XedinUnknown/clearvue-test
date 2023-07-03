<?php

declare(strict_types=1);

namespace Clearvue\Test1;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Represents an HTTP route.
 *
 * @psalm-type RequestMethod = 'GET' | 'POST' | 'PUT' | 'DELETE' | 'HEAD' | 'OPTIONS' | 'PATCH' | 'TRACE' | 'CONNECT'
 * @psalm-type RequestHandler = callable(
 *  RequestInterface,
 *  ResponseInterface,
 *  ?array<string, scalar>
 * ): ResponseInterface
 */
interface RouteInterface
{
    /**
     * Retrieve a list of methods to be handled by this instance.
     *
     * @return array<RequestMethod> A list of methods that the route will handle.
     */
    public function getMethods(): array;

    /**
     * Retrieve the path to be handled by this instance.
     *
     * @return string The path, with optional placeholders in the form `{var_name}`.
     */
    public function getPath(): string;

    /**
     * Retrieve the handler for this route.
     *
     * @return RequestHandler The handler.
     */
    public function getHandler(): callable;
}
