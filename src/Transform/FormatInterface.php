<?php

declare(strict_types=1);

namespace Clearvue\Test1\Transform;

use RuntimeException;

/**
 * Something that can decide the data format of a resource or list thereof.
 */
interface FormatInterface
{
    /**
     * Formats a single resource's data.
     *
     * @param object $resource The resource to format.
     * @param SerializerInterface $serializer Used to serialize the resource.
     *
     * @return array The formatted data.
     *
     * @throws RuntimeException If problem formatting.
     */
    public function format(object $resource, SerializerInterface $serializer): iterable;

    /**
     * Formats a list of resources.
     *
     * @param iterable<object> $list The list of resources to format.
     * @param SerializerInterface $serializer Used to serialize the resource.
     *
     * @return array The formatted list data.
     *
     * @throws RuntimeException If problem formatting.
     */
    public function formatList(iterable $list, SerializerInterface $serializer): iterable;
}
