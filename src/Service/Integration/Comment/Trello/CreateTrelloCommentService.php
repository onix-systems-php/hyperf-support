<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Comment\Trello;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Comment\CreateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\ProcessFiles;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCardApiService;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCommentApiService;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Traits\FormatHelper;
use OnixSystemsPHP\HyperfSupport\Service\Integration\TrelloService;

readonly class CreateTrelloCommentService
{
    use ProcessFiles;
    use FormatHelper;

    public function __construct(
        private TrelloCommentApiService $trelloComment,
        private CommentRepository $commentRepository,
        private TrelloCardApiService $trelloCardService,
        private TrelloService $trello,
    ) {}

    /**
     * Create a comment on Trello card.
     *
     * @param Comment $comment
     * @return Comment
     * @throws TrelloException
     * @throws GuzzleException
     */
    public function run(Comment $comment): Comment
    {
        $trelloComment = $this->trelloComment->create($comment->ticket->source, CreateCommentDTO::make([
            'card_id' => $comment->ticket->trello_id,
            'text' => $this->getCommentMessage($comment),
        ]));
        $comment = $this->commentRepository->update($comment, [
            'trello_comment_id' => $trelloComment->id,
        ]);
        $this->commentRepository->save($comment);

        return $this->processFiles($comment);
    }
}
