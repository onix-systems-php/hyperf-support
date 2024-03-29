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
use OpenApi\Attributes as OA;

use function Hyperf\Support\make;

#[OA\Schema(
    schema: 'CreateCommentRequest',
    properties: [
        new OA\Property(property: 'ticket_id', type: 'integer'),
        new OA\Property(property: 'content', type: 'string'),
        new OA\Property(property: 'creator_name', type: 'string'),
    ],
    type: 'object',
)]
readonly class CreateCommentService
{
    public function __construct(
        private ValidatorFactoryInterface $validatorFactory,
        private ?CorePolicyGuard $policyGuard,
        private CommentRepository $commentRepository,
        private EventDispatcherInterface $eventDispatcher,
        private SupportAdapter $supportAdapter
    ) {}

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
                $createCommentDTO->from,
                'is_private_discussion'
            )) {
                return $emptyComment;
            }
        }

        $this->policyGuard?->check('create', new Comment());
        $comment = $this->commentRepository->create($createCommentDTO->toArray());
        $this->commentRepository->save($comment);
        $this->eventDispatcher->dispatch(new Action(Actions::CREATE_COMMENT, $comment, $createCommentDTO->toArray()));
        $this->supportAdapter->run(Actions::CREATE_COMMENT, $comment, $shouldBeSkipped);

        return $comment;
    }

    public function validate(CreateCommentDTO $createCommentDTO): void
    {
        $this->validatorFactory->make($createCommentDTO->toArray(), [
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'content' => ['required', 'string', 'min:5'],
            'creator_name' => ['required', 'string'],
        ])->validate();
    }
}
