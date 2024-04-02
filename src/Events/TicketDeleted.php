<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Events;

use OnixSystemsPHP\HyperfSupport\Contract\TicketEvent;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

class TicketDeleted implements TicketEvent
{
    public function __construct(public Ticket $ticket) {}
}
