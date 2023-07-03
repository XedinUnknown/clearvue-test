<?php

declare(strict_types=1);

namespace Clearvue\Test1;

/**
 * An HTTP route.
 * @psalm-import-type RequestMethod from RouteInterface
 * @psalm-import-type RequestHandler from RouteInterface
 */
class Route implements RouteInterface
{
    /** @var array<RequestMethod> */
    protected array $methods;
    protected string $path;
    /** @var RequestHandler */
    protected $handler;

    /**
     * @param array<RequestMethod> $methods
     * @param string $path
     * @param RequestHandler $handler
     */
    public function __construct(array $methods, string $path, callable $handler)
    {
        $this->methods = $methods;
        $this->path = $path;
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getHandler(): callable
    {
        return $this->handler;
    }
}
