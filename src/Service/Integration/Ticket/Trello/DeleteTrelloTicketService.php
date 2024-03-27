<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Trello;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCardService;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

readonly class DeleteTrelloTicketService
{
    public function __construct(private TrelloCardService $trelloCardService) {}

    /**
     * Archive a card.
     *
     * @param Ticket $ticket
     * @return bool
     * @throws TrelloException
     * @throws GuzzleException
     */
    public function run(Ticket $ticket): bool
    {
        return $this->trelloCardService->archive($ticket->source, $ticket->trello_id);
    }
}
