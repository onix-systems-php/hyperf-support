<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\DTO\Trello\Comment;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class UpdateCommentDTO extends AbstractDTO
{
    public string $id;
    public string $card_id;
    public string $text;
}
