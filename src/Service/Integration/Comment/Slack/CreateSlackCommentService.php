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
use OnixSystemsPHP\HyperfSupport\Integration\Slack\Slack;
use OnixSystemsPHP\HyperfSupport\Integration\Slack\SlackMessage;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;
use OnixSystemsPHP\HyperfSupport\Service\Integration\Traits\FormatHelper;

readonly class CreateSlackCommentService
{
    use FormatHelper;

    public function __construct(
        private Slack $slack,
        private CommentRepository $commentRepository,
    ) {}

    /**
     * Create comment on Slack.
     *
     * @param Comment $comment
     * @return Comment
     * @throws GuzzleException
     * @throws SlackException
     */
    public function run(Comment $comment): Comment
    {
        $message = new SlackMessage();
        $message->addTextSection($this->getCommentMessage($comment));
        $message->setThreadTs($comment->ticket->slack_id);
        foreach ($comment->files as $file) {
            $externalId = $this->slack->addRemoteFile($comment->ticket->source, $file->name, $file->url);
            $message->addFile($externalId);
        }
        $response = $this->slack->postMessage($comment->ticket->source, $message);
        $this->commentRepository->update($comment, ['slack_comment_id' => $response->ts])->save();

        return $comment;
    }
}
