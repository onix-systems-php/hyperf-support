<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

class Board
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $desc,
        public readonly string $url,
        public readonly string $shortLink,
        public readonly string $shortUrl,
        public readonly array $lists
    ) {}

    /**
     * Get list id by list name.
     *
     * @param string $name
     * @return string
     */
    public function getListIdByName(string $name): string
    {
        return current(array_filter($this->lists, fn($list) => $name === $list['name']))['id'];
    }
}
