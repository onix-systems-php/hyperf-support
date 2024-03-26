<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Entity\Slack;

readonly final class UserSlack
{
    public function __construct(
        private string $id,
        private string $real_name,
    ) {}

    /**
     * Get user's identifier.
     *
     * @return string|int
     */
    public function getId(): string|int
    {
        return $this->id;
    }

    /**
     * Get user's real name.
     *
     * @return string
     */
    public function getRealName(): string
    {
        return $this->real_name;
    }
}
