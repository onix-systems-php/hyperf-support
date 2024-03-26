<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello;

use Throwable;

class TrelloCardNotFoundException extends TrelloException
{
    public function __construct(string $message = 'Trello card not found.', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
