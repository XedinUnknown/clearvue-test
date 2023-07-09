<?php

declare(strict_types=1);

namespace Clearvue\Test1\Transform;

use RuntimeException;

/**
 * Something that can decide the data format of a resource or list thereof.
 *
 * @template-covariant Out of iterable<scalar|iterable<scalar>>
 * @template In of object
 */
interface FormatInterface
{
    /**
     * Formats a single resource's data.e
     *
     * @param In $resource The resource to format.
     * @param SerializerInterface<Out, In> $serializer Used to serialize the resource.
     *
     * @return iterable<mixed> The formatted data.
     *
     * @throws RuntimeException If problem formatting.
     */
    public function format(object $resource, SerializerInterface $serializer): iterable;

    /**
     * Formats a list of resources.
     *
     * @param iterable<In> $list The list of resources to format.
     * @param SerializerInterface<Out, In> $serializer Used to serialize the resource.
     *
     * @return iterable<mixed> The formatted list data.
     *
     * @throws RuntimeException If problem formatting.
     */
    public function formatList(iterable $list, SerializerInterface $serializer): iterable;
}
