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
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\CreateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

use function Hyperf\Support\make;

class CreateCommentService
{
    public function __construct(
        private readonly ValidatorFactoryInterface $validatorFactory,
        private readonly ?CorePolicyGuard $policyGuard,
        private readonly CommentRepository $commentRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SupportAdapter $supportAdapter
    ) {
    }

    /**
     * Create a comment.
     *
     * @param CreateCommentDTO $createCommentDTO
     * @param array $shouldBeSkipped
     * @return Comment
     */
    public function run(CreateCommentDTO $createCommentDTO, array $shouldBeSkipped = []): Comment
    {
        $this->validate($createCommentDTO);

        /** @var SourceConfiguratorInterface $sourceConfigurator */
        $sourceConfigurator = make(SourceConfiguratorInterface::class);
        $emptyComment = new Comment();
        if (!is_null($createCommentDTO->source) && !is_null($createCommentDTO->from)) {
            if ($sourceConfigurator->getApiConfig(
                $createCommentDTO->source,
                'integrations',
                $createCommentDTO->from,
                'is_private_discussion'
            )) {
                return $emptyComment;
            }
        }

        $comment = $this->commentRepository->create($createCommentDTO->toArray());
        $this->policyGuard?->check('create', $comment);
        $this->commentRepository->save($comment);
        $this->eventDispatcher->dispatch(new Action(Actions::CREATE_COMMENT, $comment, $createCommentDTO->toArray()));
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
