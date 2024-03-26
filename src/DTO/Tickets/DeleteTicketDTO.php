<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\DTO\Tickets;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class DeleteTicketDTO extends AbstractDTO
{
    public int|string|null $deleted_by;
}
