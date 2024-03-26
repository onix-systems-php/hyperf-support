<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Entity\Slack;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Entity\Event;
use OnixSystemsPHP\HyperfSupport\Enum\Slack\SlackActionType;
use OnixSystemsPHP\HyperfSupport\Integration\Slack\Slack;

use function Hyperf\Support\make;

class SlackEvent extends Event
{
    public SlackActionType $type;
    public ?string $username;

    /**
     * @throws GuzzleException
     */
    public function __construct(
        public string $ts,
        public string $channel,
        string $type,
        string $user = '',
        string $subtype = '',
        public string $text = '',
        public string $thread_ts = '',
        public string $deleted_ts = '',
        public array $message = [],
        public array $files = [],
    ) {
        /** @var Slack $slack */
        $slack = make(Slack::class);
        /** @var SourceConfiguratorInterface $configurator */
        $configurator = make(SourceConfiguratorInterface::class);
        $source = $configurator->getSourceByIntegrationAndKey('slack', $this->channel);

        $this->type = SlackActionType::from($type);
        $this->username = $slack->getUser($source, $user)?->getRealName();
        if ($subtype && $subtype !== 'file_share') {
            $this->type = SlackActionType::from($subtype);
        }
        if (!empty($this->message)) {
            $this->ts = $this->message['ts'];
            $this->username = $slack->getUser($source, $this->message['user'])->getRealName();
            $this->text = $this->message['text'];
            $this->files = $this->message['files'] ?? [];
        }
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @inheritDoc
     */
    public function getEventIdentifier(): string|int
    {
        return $this->ts;
    }

    /**
     * @inheritDoc
     */
    public function getFileLinks(): array
    {
        return $this->files;
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
