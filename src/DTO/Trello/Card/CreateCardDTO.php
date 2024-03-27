<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\DTO\Trello\Card;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class CreateCardDTO extends AbstractDTO
{
    public string $name;
    public string $desc;
    public string $pos;
    public string $listName;
    public string $idMembers;
    public string $idList;
}
