<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Comment;

use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use OnixSystemsPHP\HyperfActionsLog\Event\Action;
use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfSupport\Adapter\SupportAdapter;
use OnixSystemsPHP\HyperfSupport\Constant\Actions;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\UpdateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

class UpdateCommentService
{
    public function __construct(
        private readonly ValidatorFactoryInterface $validatorFactory,
        private readonly CommentRepository $commentRepository,
        private readonly SupportAdapter $supportAdapter,
        private readonly CoreAuthenticatableProvider $coreAuthenticatableProvider,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ?CorePolicyGuard $policyGuard,
    ) {}

    /**
     * Update a comment.
     *
     * @param int $id
     * @param UpdateCommentDTO $updateCommentDTO
     * @param array $shouldBeSkipped
     * @return Comment
     */
    public function run(int $id, UpdateCommentDTO $updateCommentDTO, array $shouldBeSkipped = []): Comment
    {
        $comment = $this->commentRepository->getById($id, false, true);
        $this->validate($updateCommentDTO);

        $commentData = array_merge(
            $updateCommentDTO->toArray(),
            [
                'modified_by' => $this->coreAuthenticatableProvider->user()?->getId()
            ]
        );

        $this->commentRepository->update($comment, $commentData);
        $this->policyGuard?->check('update', $comment);
        $this->commentRepository->save($comment);
        $this->eventDispatcher->dispatch(new Action(Actions::UPDATE_COMMENT, $comment, $commentData));
        $this->supportAdapter->run(Actions::UPDATE_COMMENT, $comment, $shouldBeSkipped);

        return $comment;
    }

    private function validate(UpdateCommentDTO $updateCommentDTO): void
    {
        $this->validatorFactory->make($updateCommentDTO->toArray(), [
            'content' => ['min:5', 'string'],
        ])->validate();
    }
}
