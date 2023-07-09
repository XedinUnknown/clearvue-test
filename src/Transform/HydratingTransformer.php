<?php

declare(strict_types=1);

namespace Clearvue\Test1\Transform;

use EventSauce\ObjectHydrator\ObjectMapper;

/**
 * A transformer that uses an object mapper to deduce keys automatically.
 *
 * @see https://github.com/EventSaucePHP/ObjectHydrator
 *
 * @psalm-immutable
 * @template-covariant Out of object
 * @template-covariant In of array<array-key, mixed>
 *
 * @implements TransformerInterface<Out, In>
 */
class HydratingTransformer implements TransformerInterface
{
    protected ObjectMapper $mapper;
    /** @var class-string<Out> */
    protected string $className;

    /**
     * @param ObjectMapper $mapper The mapper used to map keys.
     * @param class-string<Out> $className The name of the class for transformation results.
     */
    public function __construct(ObjectMapper $mapper, string $className)
    {
        $this->mapper = $mapper;
        $this->className = $className;
    }

    /**
     * @inheritDoc
     *
     * @param In $value The value to transform.
     *
     * @return Out The transformation result.
     */
    public function transform(mixed $value): mixed
    {
        $mapper = $this->mapper;

        /** @psalm-suppress ImpureMethodCall */
        return $mapper->hydrateObject($this->className, $value);
    }
}
