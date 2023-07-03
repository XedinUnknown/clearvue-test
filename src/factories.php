<?php

declare(strict_types=1);

use Dhii\Services\Factories\Value;
use Dhii\Services\Service;

return function (string $mainFilePath): array {
    return [
        'clearvue/test1/main_file_path' => new Value($mainFilePath),
        'clearvue/test1/root_path' => new Factory([
            'clearvue/test1/main_file_path',
        ], function (string $mainFilePath): string {
            return dirname($mainFilePath);
        }),
        'clearvue/test1/is_debug' => new Value(true),
        'clearvue/test1/app/middleware/list' => new Value([]),
        'clearvue/test1/app/routes/list' => Service::fromFile(__DIR__ . '/routes.php'),
    ];
};
