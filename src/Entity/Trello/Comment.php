<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

class Comment
{
    public readonly string $card_id;
    public readonly string $text;

    public function __construct(public string $id, array $data)
    {
        $this->card_id = $data['card']['id'];
        $this->text = $data['text'];
    }
}
