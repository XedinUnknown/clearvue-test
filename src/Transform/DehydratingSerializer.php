<?php

declare(strict_types=1);

namespace Clearvue\Test1\Transform;

use EventSauce\ObjectHydrator\ObjectMapper;

/**
 * Can serialize objects using automatic mapping.
 *
 * @see https://github.com/EventSaucePHP/ObjectHydrator
 */
class DehydratingSerializer implements SerializerInterface
{
    /**
     * @param ObjectMapper $dehydrator The mapper used to convert objects to data.
     */
    public function __construct(
        protected ObjectMapper $dehydrator
    ) {
    }

    /**
     * @inheritDoc
     */
    public function serialize(object $object): array
    {
        return $this->dehydrator->serializeObject($object);
    }
}
