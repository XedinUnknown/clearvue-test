<?php

declare(strict_types=1);

namespace Clearvue\Test1\Transform;

use RuntimeException;

/**
 * Something that can convert an object into its data.
 *
 * @psalm-immutable
 * @template-covariant Out of array
 * @template-covariant In of object
 */
interface SerializerInterface
{
    /**
     * Serializes an object, retrieving its data.
     *
     * @param In $object The object to serialize.
     *
     * @return Out The result of serialization.
     *
     * @throws RuntimeException If problem serializing.
     */
    public function serialize(object $object): array;
}
