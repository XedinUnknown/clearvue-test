<?php

declare(strict_types=1);

use Clearvue\Test1\RouteInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;

(function (string $mainFilePath): void {
    $rootDir = dirname($mainFilePath);

    $autoload = "$rootDir/vendor/autoload.php";
    if (file_exists($autoload)) {
        /** @psalm-suppress UnresolvableInclude  */
        require_once $autoload;
    }


    /** @psalm-suppress UnresolvableInclude  */
    $bootstrap = require_once "$rootDir/src/bootstrap.php";
    /** @var ContainerInterface $appContainer */
    $appContainer = $bootstrap($mainFilePath);
    /** @var bool $isDebug */
    $isDebug = $appContainer->get('clearvue/test1/is_debug');

    $app = AppFactory::createFromContainer($appContainer);
    $app->addRoutingMiddleware();
    $app->addErrorMiddleware($isDebug, true, true);

    /** @var array<callable(RequestInterface, RequestHandlerInterface): ResponseInterface> $appMiddleware */
    $appMiddleware = $appContainer->get('clearvue/test1/app/middleware/list');
    foreach ($appMiddleware as $middleware) {
        $app->add($middleware);
    }

    /** @var iterable<RouteInterface> $appRoutes */
    $appRoutes = $appContainer->get('clearvue/test1/app/routes/list');
    foreach ($appRoutes as $route) {
        $app->map($route->getMethods(), $route->getPath(), $route->getHandler());
    }

    $app->run();
})(__FILE__);
