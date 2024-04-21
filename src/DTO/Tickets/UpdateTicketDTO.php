<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\DTO\Tickets;

use Carbon\Carbon;
use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class UpdateTicketDTO extends AbstractDTO
{
    public string $title;

    public string $content;

    public array $custom_fields;

    public ?Carbon $completed_at;

    public ?string $trello_id;

    public ?string $trello_short_link;

    public ?string $slack_id;

    public array $files;
}
