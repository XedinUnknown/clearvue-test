<?php

declare(strict_types=1);

namespace Clearvue\Test1\Transform;

use EventSauce\ObjectHydrator\KeyFormatter;
use RuntimeException;

class KeyFormatterForGetterUnprefixing implements KeyFormatter
{
    protected const GETTER_PREFIX = 'get_';

    public function __construct(
        protected KeyFormatter $innerFormatter
    ) {
    }

    /**
     * @inheritDoc
     */
    public function propertyNameToKey(string $propertyName): string
    {
        $key = $this->innerFormatter->propertyNameToKey($propertyName);
        $key = $this->filterKey($key);

        return $key;
    }

    /**
     * @inheritDoc
     */
    public function keyToPropertyName(string $key): string
    {
        return $this->innerFormatter->keyToPropertyName($key);
    }

    /**
     * Last modifications to the key.
     *
     * @param string $key The key to modify.
     *
     * @return string The possibly modified key.
     *
     * @throws RuntimeException If problem modifying.
     */
    protected function filterKey(string $key): string
    {
        // Remove prefix if key starts with it
        $prefix = static::GETTER_PREFIX;
        if (substr($key, 0, strlen($prefix)) === $prefix) {
            $key = substr($key, strlen($prefix));
        }

        return $key;
    }
}
