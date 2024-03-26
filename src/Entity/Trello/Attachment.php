<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

readonly class Attachment
{
    public function __construct(public ?string $name, public string $url, public string $id = '', public string $idMember = '') {}
}
