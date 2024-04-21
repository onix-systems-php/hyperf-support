<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Slack;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Slack\SlackException;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

class UpdateSlackTicketService
{
    public function __construct(private readonly CreateSlackTicketService $createTicketSlackService)
    {
    }

    /**
     * Update a ticket on Slack.
     *
     * @param Ticket $ticket
     * @return Ticket
     * @throws GuzzleException
     * @throws SlackException
     */
    public function run(Ticket $ticket): Ticket
    {
        $this->createTicketSlackService->run($ticket);

        return $ticket;
    }
}
