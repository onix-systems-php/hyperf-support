<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler;

use OnixSystemsPHP\HyperfSupport\Entity\Event;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

interface EventHandlerInterface
{
    /**
     * Handle event.
     *
     * @param Event $event
     * @param Ticket|Comment $entity
     * @return void
     */
    public function handle(Event $event, Ticket|Comment $entity): void;
}
