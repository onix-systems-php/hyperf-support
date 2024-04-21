<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Transport\Ticket;

use OnixSystemsPHP\HyperfSupport\Contract\TransportInterface;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Slack\CreateSlackTicketService;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Slack\DeleteSlackTicketService;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Slack\UpdateSlackTicketService;

class TicketSlackTransport implements TransportInterface
{
    public function __construct(
        private readonly CreateSlackTicketService $createTicketSlackService,
        private readonly UpdateSlackTicketService $updateTicketSlackService,
        private readonly DeleteSlackTicketService $deleteTicketSlackService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function run(string $action, Ticket|Comment $entity): Comment|Ticket|bool
    {
        return match ($action) {
            'create' => $this->createTicketSlackService->run($entity),
            'update' => $this->updateTicketSlackService->run($entity),
            'delete' => $this->deleteTicketSlackService->run($entity),
        };
    }
}
