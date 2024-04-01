<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

use function Hyperf\Collection\collect;

readonly class Card
{
    /** @var Attachment[] */
    public array $attachments;

    public function __construct(
        public string $id,
        public string $desc,
        public string $shortLink = '',
        public string $shortUrl = '',
        array $attachments = []
    ) {
        $this->attachments = array_map(function ($attachment) {
            return new Attachment(...collect($attachment)->only(['id', 'name', 'url', 'idMember'])->all());
        }, $attachments);
    }
}
