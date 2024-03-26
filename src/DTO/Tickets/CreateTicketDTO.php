<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\DTO\Tickets;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class CreateTicketDTO extends AbstractDTO
{
    public string $title;
    public string $content;
    public string $source;
    public array $custom_fields;
    public ?string $page_url;
    public int|string|null $created_by;
    public ?string $trello_id;
    public ?string $trello_short_link;
    public ?string $slack_id;
    public array $files;
}
