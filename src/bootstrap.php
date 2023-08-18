<?php

declare(strict_types=1);

use Clearvue\Test1\ServiceProvider;
use Dhii\Container\DelegatingContainer;
use Psr\Container\ContainerInterface;

return
    /**
     * @psalm-type Factory = callable(ContainerInterface): mixed
     * @psalm-type Extension = callable(ContainerInterface, mixed): mixed
     */
    function (string $mainFilePath): ContainerInterface {
        error_reporting(E_ALL);

        $rootDir = dirname($mainFilePath);
        $srcDir = "$rootDir/src";
        /**
         * @psalm-suppress UnresolvableInclude
         * @var array<string, Factory> $factories
         */
        $factories = (require_once("$srcDir/factories.php"))($mainFilePath);
        /**
         * @psalm-suppress UnresolvableInclude
         * @var array<string, Extension> $extensions
         */
        $extensions = (require_once("$srcDir/extensions.php"))();

        $services = new ServiceProvider($factories, $extensions);
        $container = new DelegatingContainer($services);

        return $container;
    };
