<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Contract;

use OnixSystemsPHP\HyperfSupport\Model\Ticket;

interface TicketDescriptionGeneratorContract
{
    /**
     * Get color according to the type and bug level of the ticket.
     *
     * @param Ticket $ticket
     * @return string
     */
     public function color(Ticket $ticket): string;

    /**
     * Get description according to the type and bug level of a ticket.
     *
     * @param Ticket $ticket
     * @return string
     */
     public function description(Ticket $ticket): string;

    /**
     * Get label according to the type and bug level of the ticket.
     *
     * @param Ticket $ticket
     * @return string
     */
     public function label(Ticket $ticket): string;

    /**
     * Get cover according to the type and bug level of the ticket.
     *
     * @param Ticket $ticket
     * @return string
     */
     public function cover(Ticket $ticket): string;

    /**
     * Get mentions of available integration according to ticket's custom_field property
     *
     * @param string $integration
     * @param Ticket $ticket
     * @return array
     */
    public function getMentionsByIntegration(string $integration, Ticket $ticket): array;

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
