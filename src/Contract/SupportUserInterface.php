<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Contract;

interface SupportUserInterface
{
    /**
     * Get user's username.
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * Get user's id.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Get user's email.
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Get user's first name.
     *
     * @return string
     */
    public function getFirstName(): string;

    /**
     * Get user's last name.
     *
     * @return string
     */
    public function getLastName(): string;
}
