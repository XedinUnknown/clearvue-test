<?php

declare(strict_types=1);

namespace Clearvue\Test1\Transform;

use EventSauce\ObjectHydrator\ObjectMapper;
use UnexpectedValueException;

/**
 * Can serialize objects using automatic mapping.
 *
 * @psalm-immutable
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
        /** @psalm-suppress ImpureMethodCall */
        $serialized = $this->dehydrator->serializeObject($object);
        if (!is_array($serialized)) {
            throw new UnexpectedValueException('Serialization did not produce a valid map');
        }

        return $serialized;
    }
}
