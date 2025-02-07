<?php

declare(strict_types=1);

namespace OnixSystemsPHP\HyperfSupport\Integration\Contract\Trello;

use OnixSystemsPHP\HyperfSupport\Integration\Contract\IntegrationDescriptionConfigContract;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

interface TrelloDescriptionConfigContract extends IntegrationDescriptionConfigContract
{
    /**
     * Get cover according to the type and bug level of the ticket.
     *
     * @param Ticket $ticket
     * @return string
     */
    public function cover(Ticket $ticket): string;

    /**
     * Get trello list from config according to ticket type.
     *
     * @param Ticket $ticket
     * @return string|null
     */
    public function getTrelloList(Ticket $ticket): ?string;

    /**
     * Check whether new ticket status is in trigger columns or not.
     *
     * @param string $source
     * @param string $status
     * @return bool
     */
    public function inTriggerLists(string $source, string $status): bool;
}
