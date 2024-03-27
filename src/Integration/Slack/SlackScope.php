<?php

namespace OnixSystemsPHP\HyperfSupport\Integration\Slack;

abstract class SlackScope
{
    protected array $fields;

    /**
     * Get options.
     *
     * @return array
     */
    abstract public function getOptions(): array;

    /**
     * Add text to message section
     *
     * @param string $text
     * @return void
     */
    public function addText(string $text): void
    {
        $this->fields[] = [
            'type' => 'mrkdwn',
            'text' => $text,
        ];
    }
}
