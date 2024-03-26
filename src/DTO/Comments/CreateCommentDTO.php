<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\DTO\Comments;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class CreateCommentDTO extends AbstractDTO
{
    public int $ticket_id;
    public string $content;
    public string $creator_name;
    public int|string|null $created_by;
    public string $trello_comment_id;
    public string $slack_comment_id;
    public array $files;
}
