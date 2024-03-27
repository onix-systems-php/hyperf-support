<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Contract;

use OnixSystemsPHP\HyperfSupport\Model\Ticket;

abstract class TicketDescriptionGeneratorBase
{
    public function __construct(readonly private SourceConfiguratorInterface $sourceConfigurator) {}

    /**
     * Get color according to the type and bug level of the ticket.
     *
     * @param Ticket $ticket
     * @return string
     */
    abstract public function color(Ticket $ticket): string;

    /**
     * Get description according to the type and bug level of a ticket.
     *
     * @param Ticket $ticket
     * @return string
     */
    abstract public function description(Ticket $ticket): string;

    /**
     * Get label according to the type and bug level of the ticket.
     *
     * @param Ticket $ticket
     * @return string
     */
    abstract public function label(Ticket $ticket): string;

    /**
     * Get cover according to the type and bug level of the ticket.
     *
     * @param Ticket $ticket
     * @return string
     */
    abstract public function cover(Ticket $ticket): string;

    /**
     * Get slack mentions from config according to the ticket's type.
     *
     * @param Ticket $ticket
     * @return array
     */
    public function slackMentions(Ticket $ticket): array
    {
        $members = $this->sourceConfigurator->getApiConfig($ticket->source, 'slack', 'mentions') ?? [];

        return match ($ticket->custom_fields['type']) {
            'Tweak', 'Feature Request' => $members[$ticket->custom_fields['type']] ?? [],
            'Bug' => $members['Bug'][$ticket->custom_fields['level']] ?? [],
            default => [],
        };
    }

    /**
     * Get trello members from config according to ticket type.
     *
     * @param Ticket $ticket
     * @return array
     */
    public function trelloMembers(Ticket $ticket): array
    {
        $members = $this->sourceConfigurator->getApiConfig($ticket->source, 'trello', 'members') ?? [];

        return $members[$ticket->custom_fields['status']] ?? $members['default'] ?? [];
    }

    /**
     * Get trello list from config according to the ticket's type.
     *
     * @param Ticket $ticket
     * @return string|null
     */
    public function trelloLists(Ticket $ticket): ?string
    {
        $columns = $this->sourceConfigurator->getApiConfig($ticket->source, 'trello', 'lists') ?? [];

        return $columns[$ticket->custom_fields['status']] ?? $columns['default'] ?? null;
    }

    /**
     * Check whether new ticket status is in trigger lists or not.
     *
     * @param string $source
     * @param string $column
     * @return bool
     */
    public function inTriggerLists(string $source, string $column): bool
    {
        return in_array($column, $this->sourceConfigurator->getApiConfig($source, 'trello', 'triggerLists'));
    }
}
