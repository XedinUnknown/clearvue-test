<?php

declare(strict_types=1);

use Dhii\Services\Factories\Value;

return function (string $mainFilePath): array {
    return [
        'clearvue/test1/main_file_path' => new Value($mainFilePath),
    ];
};
