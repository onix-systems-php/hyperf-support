<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Comment\Trello;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\Enum\TicketCreator;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCommentService;
use OnixSystemsPHP\HyperfSupport\Model\Comment;

readonly class DeleteTrelloCommentService
{
    public function __construct(private TrelloCommentService $trelloComment) {}

    /**
     * Delete a comment on Trello.
     *
     * @param Comment $comment
     * @return Comment
     * @throws TrelloException
     * @throws GuzzleException
     */
    public function run(Comment $comment): Comment
    {
        if ($this->isSystemComment($comment)) {
            return $comment;
        }
        $this->trelloComment->delete(
            $comment->ticket->source,
            $comment->ticket->trello_id,
            $comment->trello_comment_id
        );

        return $comment;
    }

    /**
     * Check whether it's a system comment.
     *
     * @param Comment $comment
     * @return bool
     */
    private function isSystemComment(Comment $comment): bool
    {
        return $comment->creator_name === TicketCreator::System->value;
    }
}
