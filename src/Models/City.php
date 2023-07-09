<?php

declare(strict_types=1);

namespace Clearvue\Test1\Models;

/**
 * Represents a city.
 */
class City
{
    /**
     * @param int $id
     * @param string $label
     */
    public function __construct(
        protected int $id,
        protected string $label
    ) {
    }

    /**
     * Retrieves the ID.
     *
     * @return int The ID.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Creates a new instance with the specified ID.
     *
     * @param int $id The ID.
     */
    public function withId(int $id): static
    {
        $me = clone $this;
        $me->id = $id;

        return $me;
    }

    /**
     * Retrieves the label.
     *
     * @return string The label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Creates a new instance with the specified label.
     *
     * @param string $label The label.
     */
    public function withLabel(string $label): static
    {
        $me = clone $this;
        $me->label = $label;

        return $me;
    }
}
