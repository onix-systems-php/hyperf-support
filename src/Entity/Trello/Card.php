<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

use function Hyperf\Collection\collect;

class Card
{
    /** @var Attachment[] */
    public readonly array $attachments;

    public function __construct(
        public readonly string $id,
        public readonly string $desc,
        public readonly string $shortLink = '',
        public readonly string $shortUrl = '',
        array $attachments = []
    ) {
        $this->attachments = array_map(function ($attachment) {
            return new Attachment(...collect($attachment)->only(['id', 'name', 'url', 'idMember'])->all());
        }, $attachments);
    }
}
