<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Comment;

use OnixSystemsPHP\HyperfActionsLog\Event\Action;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfSupport\Adapter\SupportAdapter;
use OnixSystemsPHP\HyperfSupport\Constant\Actions;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class DeleteCommentService
{
    public function __construct(
        private CommentRepository $commentRepository,
        private ?CorePolicyGuard $policyGuard,
        private EventDispatcherInterface $eventDispatcher,
        private SupportAdapter $supportAdapter
    ) {}

    /**
     * Delete a comment.
     *
     * @param int $id
     * @param array $shouldBeSkipped
     * @return bool
     */
    public function run(int $id, array $shouldBeSkipped = []): bool
    {
        $comment = $this->commentRepository->findById($id);
        $this->policyGuard?->check('delete', $comment);

        $result = $this->commentRepository->delete($comment);
        $this->eventDispatcher->dispatch(new Action(Actions::DELETE_COMMENT, $comment, $comment->toArray()));
        $this->supportAdapter->run(Actions::DELETE_COMMENT, $comment, $shouldBeSkipped);

        return $result;
    }
}
