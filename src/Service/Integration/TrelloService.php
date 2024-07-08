<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration;

use OnixSystemsPHP\HyperfSupport\Entity\Trello\TrelloEvent;
use OnixSystemsPHP\HyperfSupport\Enum\Trello\TrelloActionType;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\EventHandlerInterface;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Trello\DeleteMessageHandler;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Trello\NewMessageHandler;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Trello\UpdateMessageHandler;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Trello\UpdateTicketHandler;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;
use OnixSystemsPHP\HyperfSupport\Repository\TicketRepository;

use function Hyperf\Support\make;

class TrelloService
{
    public function __construct(
        private readonly TicketRepository $ticketRepository,
        private readonly CommentRepository $commentRepository
    ) {
    }

    /**
     * Handle the webhook.
     *
     * @param array $data
     * @return void
     */
    public function handleWebhook(array $data): void
    {
        $trelloEvent = $this->getTrelloEvent($data);
        $entity = $this->getEntity($trelloEvent);
        if (!empty($entity)) {
            $this->getHandler($trelloEvent->type)->handle($trelloEvent, $entity);
        }
    }

    /**
     * Get handler for action type.
     *
     * @param TrelloActionType $actionType
     * @return EventHandlerInterface
     */
    private function getHandler(TrelloActionType $actionType): EventHandlerInterface
    {
        $eventHandler = match ($actionType) {
            TrelloActionType::CommentCard => NewMessageHandler::class,
            TrelloActionType::UpdateComment => UpdateMessageHandler::class,
            TrelloActionType::DeleteComment => DeleteMessageHandler::class,
            TrelloActionType::UpdateCard => UpdateTicketHandler::class,
        };

        return make($eventHandler);
    }

    /**
     * Get Ticket or Comment entity.
     *
     * @param TrelloEvent $trelloEvent
     * @return Ticket|Comment|null
     */
    private function getEntity(TrelloEvent $trelloEvent): Ticket|Comment|null
    {
        return match ($trelloEvent->type) {
            TrelloActionType::CommentCard,
            TrelloActionType::UpdateCard => $this->ticketRepository->getByTrelloId($trelloEvent->cardId),

            TrelloActionType::UpdateComment,
            TrelloActionType::DeleteComment => $this->commentRepository->getByTrelloId(
                $trelloEvent->getEventIdentifier()
            ),

            default => null,
        };
    }

    /**
     * Get TrelloEvent object.
     *
     * @param array $data
     * @return TrelloEvent
     */
    private function getTrelloEvent(array $data): TrelloEvent
    {
        return new TrelloEvent(
            TrelloActionType::tryFrom($data['action']['type']),
            $data['action']['data']['action']['id'] ?? $data['action']['id'],
            $data['model']['id'],
            $data['action']['memberCreator']['fullName'] ?? '',
            $data['action']['data']['listAfter']['name'] ?? null,
            $data['action']['data']['text'] ?? $data['action']['data']['action']['text'] ?? '',
        );
    }
}
