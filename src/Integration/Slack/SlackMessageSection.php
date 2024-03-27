<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Slack;

class SlackMessageSection extends SlackScope
{
    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return [
            'type' => 'section',
            'fields' => $this->fields,
        ];
    }
}
