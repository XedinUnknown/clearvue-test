<?php

declare(strict_types=1);

use cebe\openapi\spec\OpenApi;
use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteCollectorProxyInterface;

(function (string $mainFilePath): void {
    $rootDir = dirname($mainFilePath);

    $autoload = "$rootDir/vendor/autoload.php";
    if (file_exists($autoload)) {
        /** @psalm-suppress UnresolvableInclude  */
        require_once $autoload;
    }

    // Env vars
    $config = Dotenv::createImmutable($rootDir);
    $config->load();

    // Bootstrap
    /** @psalm-suppress UnresolvableInclude  */
    $bootstrap = require_once "$rootDir/src/bootstrap.php";
    /** @var ContainerInterface $appContainer */
    $appContainer = $bootstrap($mainFilePath);

    // Initialize app
    /** @var bool $isDebug */
    $isDebug = $appContainer->get('clearvue/test1/is_debug');
    $app = AppFactory::createFromContainer($appContainer);
    $app->addRoutingMiddleware();
    $app->addErrorMiddleware($isDebug, true, true);

    /** @var OpenApi $apiSpec */
    $apiSpec = $appContainer->get('clearvue/test1/api/spec');
    /** @var MiddlewareInterface $apiValidationMiddleware */
    $apiValidationMiddleware = $appContainer->get('clearvue/test1/api/validation_middleware');

    // Register routes for all defined servers
    foreach ($apiSpec->servers as $server) {
        $apiUrl = parse_url($server->url);
        $apiHost = $apiUrl['host'] ?? null;
        $apiPath = $apiUrl['path'] ?? '/';
        $currentHost = $_SERVER['HTTP_HOST'] ?? null;

        // Only add routes for known servers or relative base URLs
        if ($apiHost !== null && $apiHost === $currentHost) {
            continue;
        }

        // Use server path to group routes
        $app->group(
            $apiPath,
            function (RouteCollectorProxyInterface $group) use (
                $apiSpec,
                $apiValidationMiddleware,
                $appContainer
            ): void {
                foreach ($apiSpec->paths as $key => $path) {
                    foreach ($path->getOperations() as $method => $operation) {
                        /** @var 'GET' | 'POST' $methodName */
                        $methodName = strtoupper($method);
                        $handler = $operation->operationId;

                        $group->map([$methodName], $key, $handler)
                            ->add($apiValidationMiddleware);
                    }
                }
            }
        );
    }

    $app->run();
})(__FILE__);
