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
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfSupport\Adapter\SupportAdapter;
use OnixSystemsPHP\HyperfSupport\Constant\Actions;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\UpdateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateCommentRequest',
    properties: [
        new OA\Property(property: 'content', type: 'string'),
    ],
    type: 'object',
)]
readonly class UpdateCommentService
{
    public function __construct(
        private ValidatorFactoryInterface $validatorFactory,
        private CommentRepository $commentRepository,
        private ?CorePolicyGuard $policyGuard,
        private SupportAdapter $supportAdapter,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    /**
     * Update a comment.
     *
     * @param int $id
     * @param UpdateCommentDTO $updateCommentDTO
     * @param array $shouldBeSkipped
     * @return Comment|null
     */
    public function run(int $id, UpdateCommentDTO $updateCommentDTO, array $shouldBeSkipped = []): ?Comment
    {
        $comment = $this->commentRepository->findById($id);
        $this->validate($updateCommentDTO);

        $this->policyGuard?->check('update', $comment);
        $this->commentRepository->update($comment, $updateCommentDTO->toArray());
        $this->commentRepository->save($comment);
        $this->eventDispatcher->dispatch(
            new Action(Actions::UPDATE_COMMENT, $comment, $updateCommentDTO->toArray())
        );
        $this->supportAdapter->run(Actions::UPDATE_COMMENT, $comment, $shouldBeSkipped);

        return $comment;
    }

    public function validate(UpdateCommentDTO $updateCommentDTO): void
    {
        $this->validatorFactory->make($updateCommentDTO->toArray(), [
            'content' => ['min:5', 'string'],
        ])->validate();
    }
}
