<?php

declare(strict_types=1);

namespace Clearvue\Test1\Transform;

use Clearvue\Test1\Db\SelectResultInterface;

/**
 * Formats a model selection with additional keys.
 *
 * The list and resource data will always be under the configured data key.
 * The list and resource meta will always be under the confirured meta key.
 *
 * This approach allows for separation of keys used for the different purposes.
 */
class DataKeyFormat implements FormatInterface
{
    /** The key used to denote the resource type */
    protected const KEY_RESOURCE_TYPE = 'type';

    /**
     * @param string $dataKey The key used for resource data.
     * @param string $metaKey The key used for resource meta.
     */
    public function __construct(
        protected string $dataKey,
        protected string $metaKey
    ) {
    }

    /**
     * @inheritDoc
     */
    public function format(object $resource, SerializerInterface $serializer): iterable
    {
        $data = $serializer->serialize($resource);
        yield $this->dataKey => $data;

        $meta = [];
        $meta[static::KEY_RESOURCE_TYPE] = $this->getResourceType($resource);
        yield $this->metaKey => $meta;
    }

    /**
     * @inheritDoc
     */
    public function formatList(iterable $list, SerializerInterface $serializer): iterable
    {
        yield $this->dataKey => function () use ($list, $serializer): iterable {
            foreach ($list as $item) {
                yield $this->format($item, $serializer);
            }
        };

        $meta = [];

        if ($list instanceof SelectResultInterface) {
            $meta['totalRecords'] = $list->getFoundRowsCount();
        }

        if (count($meta)) {
            yield $this->metaKey => $meta;
        }
    }

    /**
     * Retrieves the type of the resource provided.
     *
     * @param object $item The resource to get the type for.
     *
     * @return string The type name.
     */
    protected function getResourceType(object $item): string
    {
        $fqn = get_class($item);
        $fqnSegments = explode('\\', $fqn);
        $type = array_pop($fqnSegments);
        $type = strtolower($type);

        return $type;
    }
}
