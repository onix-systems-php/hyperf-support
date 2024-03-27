<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Slack;

use OnixSystemsPHP\HyperfSupport\Model\Ticket;

readonly class DeleteSlackTicketService
{
    public function __construct(private CreateSlackTicketService $createTicketSlackService) {}

    /**
     * Archive a ticket on Slack.
     *
     * @param Ticket $ticket
     * @return Ticket
     */
    public function run(Ticket $ticket): Ticket
    {
        $this->createTicketSlackService->run($ticket);

        return $ticket;
    }
}
