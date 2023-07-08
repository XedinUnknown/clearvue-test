<?php

declare(strict_types=1);

namespace Clearvue\Test1\Transform;

/**
 * A transformer that applies a transformation callback.
 *
 * @psalm-immutable
 * @template-covariant Out
 * @template-covariant In
 *
 * @implements TransformerInterface<Out, In>
 */
class CallbackTransformer implements TransformerInterface
{
    /** @var callable(In): Out */
    protected $callback;

    /**
     * @param callable(In): Out $callback The transformation callback.
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     *
     * @param In $value The value to transform.
     * @return Out The transformation result.
     */
    public function transform($value)
    {
        $callback = $this->callback;

        /** @psalm-suppress ImpureFunctionCall */
        return $callback($value);
    }
}
