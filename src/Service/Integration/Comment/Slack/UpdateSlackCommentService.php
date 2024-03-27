<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Comment\Slack;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Slack\SlackException;
use OnixSystemsPHP\HyperfSupport\Integration\Slack\SlackApiService;
use OnixSystemsPHP\HyperfSupport\Integration\Slack\SlackMessage;
use OnixSystemsPHP\HyperfSupport\Model\Comment;

readonly class UpdateSlackCommentService
{
    public function __construct(private SlackApiService $slack) {}

    /**
     * Update comment on Slack.
     *
     * @param Comment $comment
     * @return Comment
     * @throws GuzzleException
     * @throws SlackException
     */
    public function run(Comment $comment): Comment
    {
        $message = new SlackMessage($comment->ticket->source);
        $message->setPlainText($comment->content);
        $message->setTs($comment->slack_comment_id);
        $this->slack->updateMessage($comment->ticket->source, $message);

        return $comment;
    }
}
