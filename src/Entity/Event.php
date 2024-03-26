<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Entity;

abstract class Event
{
    /**
     * Get a username.
     *
     * @return string
     */
    abstract public function getUsername(): string;

    /**
     * Get the text.
     *
     * @return string
     */
    abstract public function getText(): string;

    /**
     * Get an event identifier.
     *
     * @return string|int
     */
    abstract public function getEventIdentifier(): string|int;

    /**
     * Get the file links.
     *
     * @return array
     */
    abstract public function getFileLinks(): array;

    /**
     * Get a ticket's status.
     *
     * @return string|null
     */
    public function getTicketStatus(): ?string
    {
        return null;
    }
}
