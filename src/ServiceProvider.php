<?php

declare(strict_types=1);

namespace Clearvue\Test1;

use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

/**
 * A generic service provider.
 *
 * @psalm-type Factory = callable(ContainerInterface): mixed
 * @psalm-type Extension = callable(ContainerInterface, mixed): mixed
 */
class ServiceProvider implements ServiceProviderInterface
{
    /** @var array<string, Factory>  */
    protected array $factories;
    /** @var array<string, Extension>  */
    protected array $extensions;

    /**
     * @psalm-param array<string, Factory> $factories
     * @param array<string, Extension> $extensions
     */
    public function __construct(array $factories, array $extensions)
    {
        $this->factories = $factories;
        $this->extensions = $extensions;
    }

    /**
     * @inheritDoc
     */
    public function getFactories()
    {
        return $this->factories;
    }

    /**
     * @inheritDoc
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
}
