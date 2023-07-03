<?php

declare(strict_types=1);

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use Dhii\Services\Factories\StringService;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use League\OpenAPIValidation\PSR15\ValidationMiddlewareBuilder;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

return function (string $mainFilePath): array {
    return [
        'clearvue/test1/main_file_path' => new Value($mainFilePath),
        'clearvue/test1/root_path' => new Factory([
            'clearvue/test1/main_file_path',
        ], function (string $mainFilePath): string {
            return dirname($mainFilePath);
        }),
        'clearvue/test1/is_debug' => new Value(true),
        'clearvue/test1/api/spec_path' => new StringService('{0}/openapi.yml', [
            'clearvue/test1/root_path',
        ]),
        'clearvue/test1/api/spec' => new Factory([
            'clearvue/test1/api/spec_path',
        ], function (string $specPath): OpenApi {
            return Reader::readFromYamlFile($specPath);
        }),
        'clearvue/test1/api/validation_middleware' => new Factory([
            'clearvue/test1/api/spec',
        ], function (OpenApi $spec): MiddlewareInterface {
            $validationMiddleware = (new ValidationMiddlewareBuilder())
                ->fromSchema($spec)
                ->getValidationMiddleware();

            return $validationMiddleware;
        }),
        'clearvue/test1/api/handlers/categories/list' => new Factory([
        ], function () {
            return function (RequestInterface $request, ResponseInterface $response): ResponseInterface {
                $categories = [];

                $response = $response->withAddedHeader('Content-Type', 'application/json');
                $response->getBody()
                    ->write((string) json_encode($categories));

                return $response;
            };
        }),
    ];
};
