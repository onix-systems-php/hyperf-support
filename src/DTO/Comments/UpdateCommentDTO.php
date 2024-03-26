<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\DTO\Comments;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class UpdateCommentDTO extends AbstractDTO
{
    public string $content;
    public int|string|null $modified_by;
    public ?string $trello_id;
    public ?string $slack_id;
    public array $files;
}
