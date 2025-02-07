<?php

declare(strict_types=1);

namespace OnixSystemsPHP\HyperfSupport\Integration\Contract\Slack;

use OnixSystemsPHP\HyperfSupport\Integration\Contract\IntegrationDescriptionConfigContract;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

interface SlackDescriptionConfigContract extends IntegrationDescriptionConfigContract
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
}
