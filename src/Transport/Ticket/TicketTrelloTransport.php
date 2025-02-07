<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Transport\Ticket;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use OnixSystemsPHP\HyperfSupport\Contract\TransportInterface;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Trello\CreateTrelloTicketService;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Trello\DeleteTrelloTicketService;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Trello\UpdateTrelloTicketService;

class TicketTrelloTransport implements TransportInterface
{
    public function __construct(
        private readonly CreateTrelloTicketService $createTicketTrelloService,
        private readonly UpdateTrelloTicketService $updateTicketTrelloService,
        private readonly DeleteTrelloTicketService $deleteTicketTrelloService
    ) {
    }

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
            default => throw new BusinessException(500, 'Unknown action type'),
        };
    }
}
