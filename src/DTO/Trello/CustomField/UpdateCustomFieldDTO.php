<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\DTO\Trello\CustomField;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class UpdateCustomFieldDTO extends AbstractDTO
{
    public string $card_id;
    public ?string $field_id;
    public ?string $option_id;
}
