<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Transport\Comment;

use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use OnixSystemsPHP\HyperfSupport\Contract\TransportInterface;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Comment\Slack\CreateSlackCommentService;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Comment\Slack\DeleteSlackCommentService;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Comment\Slack\UpdateSlackCommentService;

class CommentSlackTransport implements TransportInterface
{
    public function __construct(
        private readonly CreateSlackCommentService $createCommentSlackService,
        private readonly UpdateSlackCommentService $updateCommentSlackService,
        private readonly DeleteSlackCommentService $deleteCommentSlackService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function run(string $action, Ticket|Comment $entity): Ticket|Comment
    {
        return match ($action) {
            'create' => $this->createCommentSlackService->run($entity),
            'update' => $this->updateCommentSlackService->run($entity),
            'delete' => $this->deleteCommentSlackService->run($entity),
            default => throw new BusinessException(500, 'Unknown action type'),
        };
    }
}
