<?php

declare(strict_types=1);

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use Clearvue\Test1\Codec\CachingEncoder;
use Clearvue\Test1\Codec\JsonStreamingEncoder;
use Clearvue\Test1\Codec\StreamingEncoderInterface;
use Clearvue\Test1\Commands\ListCommand;
use Clearvue\Test1\Handlers\ListHandler;
use Clearvue\Test1\Models\City;
use Clearvue\Test1\Transform\DataKeyFormat;
use Clearvue\Test1\Transform\DehydratingSerializer;
use Clearvue\Test1\Transform\FormatInterface;
use Clearvue\Test1\Transform\HydratingTransformer;
use Clearvue\Test1\Transform\KeyFormatterForGetterUnprefixing;
use Clearvue\Test1\Transform\TransformerInterface;
use Dhii\Container\Dictionary;
use Dhii\Services\Factories\Alias;
use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\StringService;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use EventSauce\ObjectHydrator\DefinitionProvider;
use EventSauce\ObjectHydrator\KeyFormatter;
use EventSauce\ObjectHydrator\KeyFormatterForSnakeCasing;
use EventSauce\ObjectHydrator\ObjectMapper;
use EventSauce\ObjectHydrator\ObjectMapperUsingReflection;
use League\OpenAPIValidation\PSR15\ValidationMiddlewareBuilder;
use Psr\Container\ContainerInterface;
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
        'clearvue/test1/api/is_pretty_print' => new Alias('clearvue/test1/is_debug'),
        'clearvue/test1/api/codecs/streaming_json_encoder' => new Factory([
            'clearvue/test1/api/is_pretty_print',
        ], function (bool $isPrettyPrint) {
            return new JsonStreamingEncoder($isPrettyPrint);
        }),
        'clearvue/test1/api/codec' => new Factory([
            'clearvue/test1/api/codecs/streaming_json_encoder',
        ], function (StreamingEncoderInterface $encoder): StreamingEncoderInterface {
            return new CachingEncoder($encoder);
        }),
        'clearvue/test1/data/hydration_key_formatter' => new Factory([
        ], function (): KeyFormatter {
            return new KeyFormatterForGetterUnprefixing(new KeyFormatterForSnakeCasing());
        }),
        'clearvue/test1/data/hydration_mapper' => new Factory([
            'clearvue/test1/data/hydration_key_formatter',
        ], function (KeyFormatter $keyFormatter): ObjectMapper {
            return new ObjectMapperUsingReflection(new DefinitionProvider(keyFormatter: $keyFormatter));
        }),
        'clearvue/test1/data/transformer/city_hydrator' => new Factory([
            'clearvue/test1/data/hydration_mapper',
        ], function (ObjectMapper $mapper): TransformerInterface {
            return new HydratingTransformer($mapper, City::class);
        }),
        'clearvue/test1/data/dehydrating_serializer' => new Constructor(DehydratingSerializer::class, [
            'clearvue/test1/data/hydration_mapper',
        ]),
        'clearvue/test1/api/serializer/city_serializer' => new Alias('clearvue/test1/data/dehydrating_serializer'),
        'clearvue/test1/api/handlers/city/list' => new Constructor(ListHandler::class, [
            'clearvue/test1/data/commands/city/list',
            'clearvue/test1/api/codec',
            'clearvue/test1/api/serializer/city_serializer',
            'clearvue/test1/api/format',
        ]),
        'clearvue/test1/api/formats/data_key_format' => new Factory([], function (): FormatInterface {
            return new DataKeyFormat('data', 'meta');
        }),
        'clearvue/test1/api/format' => new Alias('clearvue/test1/api/formats/data_key_format'),
        'clearvue/test1/env' => new Factory([
        ], function (): ContainerInterface {
            $env = $_ENV;
            $map = new Dictionary($env);

            return $map;
        }),
        'clearvue/test1/db/primary/settings' => new Factory([
            'clearvue/test1/env',
        ], function (ContainerInterface $env): ContainerInterface {
            return new Dictionary([
                'type' => 'mysql',
                'host' => $env->get('DB_HOST'),
                'port' => $env->get('DB_PORT'),
                'name' => $env->get('DB_NAME'),
                'username' => $env->get('DB_USER_NAME'),
                'password' => $env->get('DB_USER_PASSWORD'),
            ]);
        }),

        'clearvue/test1/db/primary/connection' => new Factory([
            'clearvue/test1/db/primary/settings',
        ], function (ContainerInterface $settings): PDO {
            $dsn = sprintf(
                '%1$s:host=%2$s;dbname=%3$s',
                (string) $settings->get('type'),
                (string) $settings->get('host'),
                (string) $settings->get('name')
            );
            $connection = new PDO(
                $dsn,
                (string) $settings->get('username'),
                (string) $settings->get('password')
            );
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $connection;
        }),
        'clearvue/test1/data/commands/city/list' => new Factory([
            'clearvue/test1/db/primary/connection',
            'clearvue/test1/data/transformer/city_hydrator',
        ], function (PDO $connection, TransformerInterface $hydrator): ListCommand {
            return new ListCommand($connection, 'city', $hydrator);
        }),
    ];
};
