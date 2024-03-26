<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Slack;

final class UserSlack
{
    public function __construct(
        private readonly string $id,
        private readonly string $real_name,
    ) {
    }

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
