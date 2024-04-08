<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Contract;

/**
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $role
 */
interface SupportUserInterface
{
    /**
     * @return string
     */
    public function getUsername(): string;
}
