<?php

declare(strict_types=1);

namespace Clearvue\Test1\Transform;

use RuntimeException;

/**
 * Something that can transform one data type into another.
 *
 * @psalm-immutable
 *
 * @template-covariant Out
 * @template-covariant In
 */
interface TransformerInterface
{
    /**
     * @param In $value The value to transform.
     *
     * @return Out The result of the transformation.
     *
     * @throws RuntimeException If problem transforming.
     */
    public function transform($value);
}
