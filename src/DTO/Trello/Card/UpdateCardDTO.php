<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\DTO\Trello\Card;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Cover\CreateCardCoverDTO;

class UpdateCardDTO extends AbstractDTO
{
    public string $name;
    public string $desc;
    public string $pos;
    public bool $closed;
    public CreateCardCoverDTO $cover;
}
