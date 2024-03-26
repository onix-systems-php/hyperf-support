<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Constant;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

#[Constants]
class Actions extends AbstractConstants
{
    public const CREATE_TICKET = 'create-ticket';
    public const UPDATE_TICKET = 'update-ticket';
    public const DELETE_TICKET = 'delete-ticket';

    public const CREATE_COMMENT = 'create-comment';
    public const UPDATE_COMMENT = 'update-comment';
    public const DELETE_COMMENT = 'delete-comment';
}
