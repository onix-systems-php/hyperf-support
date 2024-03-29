<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Transport\Ticket;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\Contract\TransportInterface;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Trello\CreateTrelloTicketService;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Trello\DeleteTrelloTicketService;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Trello\UpdateTrelloTicketService;

readonly class TicketTrelloTransport implements TransportInterface
{
    public function __construct(
        private CreateTrelloTicketService $createTicketTrelloService,
        private UpdateTrelloTicketService $updateTicketTrelloService,
        private DeleteTrelloTicketService $deleteTicketTrelloService
    ) {}

    /**
     * @inheritDoc
     * @throws TrelloException
     * @throws GuzzleException
     */
    public function run(string $action, Ticket|Comment $entity): Ticket|Comment|bool
    {
        return match ($action) {
            'create' => $this->createTicketTrelloService->run($entity),
            'update' => $this->updateTicketTrelloService->run($entity),
            'delete' => $this->deleteTicketTrelloService->run($entity),
        };
    }
}
