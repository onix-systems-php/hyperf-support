<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Slack;

class SlackMessageContext extends SlackScope
{
    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return [
            'type' => 'context',
            'elements' => $this->fields,
        ];
    }

    /**
     * Add image to message context.
     *
     * @param string $url
     * @param string $alt
     * @return void
     */
    public function addImage(?string $url, string $alt = ''): void
    {
        if ($url) {
            $this->fields[] = [
                'type' => 'image',
                'image_url' => $url,
                'alt_text' => $alt,
            ];
        }
    }
}
