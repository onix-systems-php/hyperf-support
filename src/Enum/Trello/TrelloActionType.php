<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Enum\Trello;

enum TrelloActionType: string
{
    case CommentCard = 'commentCard';
    case UpdateComment = 'updateComment';
    case DeleteComment = 'deleteComment';
    case UpdateCard = 'updateCard';
}
