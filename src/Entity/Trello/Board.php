<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

readonly class Board
{
    public function __construct(
        public string $id,
        public string $name,
        public string $desc,
        public string $url,
        public string $shortLink,
        public string $shortUrl,
        public array $lists
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
