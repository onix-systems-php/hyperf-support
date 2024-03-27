<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Transport;

use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

interface TransportInterface
{
    /**
     * Run the Ticket or Comment transport.
     *
     * @param string $action
     * @param Ticket|Comment $entity
     * @return Ticket|Comment|bool
     */
    public function run(string $action, Ticket|Comment $entity): Ticket|Comment|bool;
}
