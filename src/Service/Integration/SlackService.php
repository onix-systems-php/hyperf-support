<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration;

use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Collection\Collection;
use OnixSystemsPHP\HyperfSupport\Entity\Slack\SlackEvent;
use OnixSystemsPHP\HyperfSupport\Enum\Slack\SlackActionType;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\EventHandlerInterface;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Slack\DeleteMessageHandler;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Slack\NewMessageHandler;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Slack\UpdateMessageHandler;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;
use OnixSystemsPHP\HyperfSupport\Repository\TicketRepository;

use function Hyperf\Collection\collect;
use function Hyperf\Support\make;

class SlackService
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
     * @throws GuzzleException
     */
    public function handleWebhook(array $data): void
    {
        $slackEvent = $this->getSlackEvent(collect($data));
        $entity = $this->getEntity($slackEvent);
        if (!empty($entity)) {
            $this->getHandler($slackEvent->type)->handle($slackEvent, $entity);
        }
    }

    /**
     * Get entity.
     *
     * @param SlackEvent $slackEvent
     * @return Ticket|Comment|null
     */
    private function getEntity(SlackEvent $slackEvent): Ticket|Comment|null
    {
        return match ($slackEvent->type) {
            SlackActionType::Message => $this->ticketRepository->getBySlackId($slackEvent->thread_ts),
            SlackActionType::UpdateMessage => $this->commentRepository->getBySlackCommentId($slackEvent->ts),
            SlackActionType::DeleteMessage => $this->commentRepository->getBySlackCommentId($slackEvent->deleted_ts)
        };
    }

    /**
     * Get Handler.
     *
     * @param SlackActionType $actionType
     * @return EventHandlerInterface
     */
    private function getHandler(SlackActionType $actionType): EventHandlerInterface
    {
        $eventHandler = match ($actionType) {
            SlackActionType::Message => NewMessageHandler::class,
            SlackActionType::UpdateMessage => UpdateMessageHandler::class,
            SlackActionType::DeleteMessage => DeleteMessageHandler::class,
        };

        return make($eventHandler);
    }

    /**
     * Generate a SlackEvent class.
     *
     * @param Collection $data
     * @return SlackEvent
     * @throws GuzzleException
     * @throws GuzzleException
     */
    private function getSlackEvent(Collection $data): SlackEvent
    {
        return new SlackEvent(
            ...
            collect($data->get('event'))->only(
                ['type', 'channel', 'user', 'subtype', 'ts', 'text', 'thread_ts', 'deleted_ts', 'message', 'files']
            )->all()
        );
    }
}
