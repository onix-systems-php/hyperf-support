<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Slack;

use OnixSystemsPHP\HyperfSupport\DTO\Comments\UpdateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Event;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\EventHandlerInterface;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Comment\UpdateCommentService;
use OnixSystemsPHP\HyperfSupport\Transport\Comment\CommentSlackTransport;

class UpdateMessageHandler implements EventHandlerInterface
{
    public function __construct(private readonly UpdateCommentService $updateCommentService)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Event $event, Ticket|Comment $entity): void
    {
        $this->updateCommentService->run($entity->id, UpdateCommentDTO::make([
            'content' => $event->getText(),
            'slack_id' => $event->getEventIdentifier(),
        ]), [CommentSlackTransport::class]);
    }
}
