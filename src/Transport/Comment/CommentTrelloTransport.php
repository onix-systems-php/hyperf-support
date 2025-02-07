<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Transport\Comment;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use OnixSystemsPHP\HyperfSupport\Contract\TransportInterface;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Comment\Trello\CreateTrelloCommentService;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Comment\Trello\DeleteTrelloCommentService;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Comment\Trello\UpdateTrelloCommentService;

class CommentTrelloTransport implements TransportInterface
{
    public function __construct(
        private readonly CreateTrelloCommentService $createCommentTrelloService,
        private readonly UpdateTrelloCommentService $updateCommentTrelloService,
        private readonly DeleteTrelloCommentService $deleteCommentTrelloService
    ) {
    }

    /**
     * @inheritDoc
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function run(string $action, Ticket|Comment $entity): Ticket|Comment
    {
        return match ($action) {
            'create' => $this->createCommentTrelloService->run($entity),
            'update' => $this->updateCommentTrelloService->run($entity),
            'delete' => $this->deleteCommentTrelloService->run($entity),
            default => throw new BusinessException(500, 'Unknown action type'),
        };
    }
}
