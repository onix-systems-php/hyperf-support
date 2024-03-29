<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Trello;

use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\DTO\Tickets\UpdateTicketDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Event;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\EventHandlerInterface;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Ticket\UpdateTicketService;

use function Hyperf\Support\make;

readonly class UpdateTicketHandler implements EventHandlerInterface
{
    public function __construct(private UpdateTicketService $updateTicketService) {}

    /**
     * @inheritDoc
     */
    public function handle(Event $event, Ticket|Comment $entity): void
    {
        /** @var SourceConfiguratorInterface $sourceConfigurator */
        $sourceConfigurator = make(SourceConfiguratorInterface::class);
        if (!is_null($event->getTicketStatus())) {
            $this->updateTicketService->run($entity->id, UpdateTicketDTO::make([
                'custom_fields' => [
                    'status' => array_flip(
                        $sourceConfigurator->getApiConfig($entity->source, 'integrations', 'trello', 'columns')
                    )[$event->getTicketStatus()]
                ],
            ]));
        }
    }
}
