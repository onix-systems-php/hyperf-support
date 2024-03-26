<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

readonly class Comment
{
    public string $card_id;
    public string $text;

    public function __construct(public string $id, array $data)
    {
        $this->card_id = $data['card']['id'];
        $this->text = $data['text'];
    }
}
