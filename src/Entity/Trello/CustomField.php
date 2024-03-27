<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

use OnixSystemsPHP\HyperfSupport\Entity\Trello\Options\Option;

class CustomField
{
    /** @var Option[]  */
    public array $options;

    public function __construct(public string $id, public string $name, array $options)
    {
        $this->options = array_map(
            fn($option) => new Option(
                $option['id'],
                $option['idCustomField'],
                $option['color'],
                $option['value']['text']
            ),
            $options
        );
    }
}
