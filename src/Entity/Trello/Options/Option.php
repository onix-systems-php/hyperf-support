<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello\Options;

class Option
{
    public OptionValue $value;

    public function __construct(public string $id, public string $customFieldId, public string $color, string $value)
    {
        $this->value = new OptionValue($value);
    }
}
