<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

class Attachment
{
    public function __construct(
        public readonly ?string $name,
        public readonly string $url,
        public readonly string $id = '',
        public readonly string $idMember = ''
    ) {}
}
