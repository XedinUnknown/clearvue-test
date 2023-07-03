<?php

declare(strict_types=1);

(function (string $mainFilePath): void {
    $rootDir = dirname($mainFilePath);

    $autoload = "$rootDir/vendor/autoload.php";
    if (file_exists($autoload)) {
        /** @psalm-suppress UnresolvableInclude  */
        require_once $autoload;
    }


    /** @psalm-suppress UnresolvableInclude  */
    $bootstrap = require_once "$rootDir/src/bootstrap.php";
    $appContainer = $bootstrap($mainFilePath);
})(__FILE__);
