<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Traits;

use OnixSystemsPHP\HyperfSupport\Model\Comment;

trait FormatHelper
{
    /**
     * Get formatted comment message.
     *
     * @param Comment $comment
     * @return string
     */
    private function getCommentMessage(Comment $comment): string
    {
        return sprintf("_[%s]_\n\n\n%s", $comment->creator_name, $comment->content);
    }

    /**
     * Get original comment message if possible.
     *
     * @param Comment $comment
     * @return string
     */
    private function getOriginalMessage(Comment $comment): string
    {
        $creator = "_[{$comment->creator_name}]_";

        if (str_starts_with($comment->content, $creator)) {
            return trim(substr($comment->content, strlen($creator)));
        }

        return $comment->content;
    }
}
