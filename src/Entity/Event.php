<?php

namespace OnixSystemsPHP\HyperfSupport\Entity;

abstract class Event
{
    /**
     * Get username.
     *
     * @return string
     */
    abstract public function getUsername(): string;

    /**
     * Get text.
     *
     * @return string
     */
    abstract public function getText(): string;

    /**
     * Get event identifier.
     *
     * @return string|int
     */
    abstract public function getEventIdentifier(): string|int;

    /**
     * Get file links.
     *
     * @return array
     */
    abstract public function getFileLinks(): array;

    /**
     * Get ticket's status.
     *
     * @return string|null
     */
    public function getTicketStatus(): ?string
    {
        return null;
    }
}
