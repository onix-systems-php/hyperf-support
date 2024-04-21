<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Comment\Trello;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Comment\UpdateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCommentApiService;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Traits\FormatHelper;

class UpdateTrelloCommentService
{
    use FormatHelper;

    public function __construct(private readonly TrelloCommentApiService $trelloComment)
    {
    }

    /**
     * Update a comment on Trello card.
     *
     * @param Comment $comment
     * @return Comment
     * @throws TrelloException
     * @throws GuzzleException
     */
    public function run(Comment $comment): Comment
    {
        $this->trelloComment->update($comment->ticket->source, UpdateCommentDTO::make([
            'id' => $comment->trello_comment_id,
            'card_id' => $comment->ticket->trello_id,
            'text' => $this->getCommentMessage($comment),
        ]));

        return $comment;
    }
}
