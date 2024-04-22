<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Slack;

use OnixSystemsPHP\HyperfSupport\Entity\Event;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\EventHandlerInterface;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Comment\DeleteCommentService;
use OnixSystemsPHP\HyperfSupport\Transport\Comment\CommentSlackTransport;

class DeleteMessageHandler implements EventHandlerInterface
{
    public function __construct(private readonly DeleteCommentService $deleteCommentService)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Event $event, Ticket|Comment $entity): void
    {
        $this->deleteCommentService->run($entity->id, [CommentSlackTransport::class], true);
    }
}
