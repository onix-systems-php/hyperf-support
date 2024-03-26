<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

use OnixSystemsPHP\HyperfSupport\Entity\Trello\Options\Option;

class CustomField
{
    public array $options;

    public function __construct(public string $id, public string $name, array $options)
    {
        $this->options = array_map(
            fn($option) => new Option(
                id: $option['id'],
                customFieldId: $option['idCustomField'],
                color: $option['color'],
                value: $option['value']['text']
            ),
            $options
        );
    }
}
