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
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\CreateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

class CreateCommentService
{
    public function __construct(
        private readonly ValidatorFactoryInterface $validatorFactory,
        private readonly CommentRepository $commentRepository,
        private readonly SupportAdapter $supportAdapter,
        private readonly CoreAuthenticatableProvider $coreAuthenticatableProvider,
        private readonly SourceConfiguratorInterface $sourceConfigurator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ?CorePolicyGuard $policyGuard,
    ) {
    }

    /**
     * Create a comment.
     *
     * @param CreateCommentDTO $createCommentDTO
     * @param array $shouldBeSkipped
     * @param bool $internalCall
     *
     * @return Comment
     */
    public function run(
        CreateCommentDTO $createCommentDTO,
        array $shouldBeSkipped = [],
        bool $internalCall = false
    ): Comment {
        $this->validate($createCommentDTO);

        if (!is_null($createCommentDTO->source) && !is_null($createCommentDTO->from)) {
            if ($this->sourceConfigurator->getApiConfig(
                $createCommentDTO->source,
                'integrations',
                $createCommentDTO->from,
                'is_private_discussion'
            )) {
                return new Comment();
            }
        }

        $commentData = array_merge(
            $createCommentDTO->toArray(),
            [
                'created_by' => $this->coreAuthenticatableProvider->user()?->getId(),
            ]
        );

        $comment = $this->commentRepository->create($commentData);
        $this->policyGuard?->check('create', $comment, ['internalCall' => $internalCall]);
        $this->commentRepository->save($comment);
        $this->eventDispatcher->dispatch(new Action(Actions::CREATE_COMMENT, $comment, $commentData));
        $this->supportAdapter->run(Actions::CREATE_COMMENT, $comment, $shouldBeSkipped);

        return $comment;
    }

    private function validate(CreateCommentDTO $createCommentDTO): void
    {
        $this->validatorFactory->make($createCommentDTO->toArray(), [
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'content' => ['required', 'string'],
            'creator_name' => ['required', 'string'],
        ])->validate();
    }
}
