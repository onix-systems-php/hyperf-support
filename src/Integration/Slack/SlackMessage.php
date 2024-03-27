<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Slack;

use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;

use function Hyperf\Support\make;

class SlackMessage
{
    private ?string $text = null;
    private array $blocks = [];
    private array $attachments = [];
    private ?string $channelId;
    private ?string $threadTs = null;
    private ?string $ts = null;

    private SourceConfiguratorInterface $sourceConfigurator;

    public function __construct(private readonly string $source, ?string $header = null)
    {
        /** @var SourceConfiguratorInterface $sourceConfigurator */
        $this->sourceConfigurator = make(SourceConfiguratorInterface::class);
        $this->channelId = $this->sourceConfigurator->getApiConfig($this->source, 'slack', 'channel_id');
        if ($header) {
            $this->addHeader($header);
        }
    }

    /**
     * Get options on SlackMessage.
     *
     * @return array
     */
    public function getOptions(): array
    {
        $options = [
            'channel' => $this->channelId,
            'text' => $this->text,
            'blocks' => $this->blocks,
            'attachments' => $this->attachments,
        ];

        if ($this->threadTs) {
            $options['thread_ts'] = $this->threadTs;
        }
        if ($this->ts) {
            $options['ts'] = $this->ts;
        }

        return $options;
    }

    /**
     * Set plain text.
     *
     * @param string|null $text
     * @return void
     */
    public function setPlainText(?string $text): void
    {
        $this->text = $this->stripLinks($text);
    }

    /**
     * Set thread ts on Slack message.
     *
     * @param string $threadTs
     * @return void
     */
    public function setThreadTs(string $threadTs): void
    {
        $this->threadTs = $threadTs;
    }

    /**
     * Set timestamp of message (ts)
     *
     * @param string $ts
     * @return void
     */
    public function setTs(string $ts): void
    {
        $this->ts = $ts;
    }

    /**
     * Get timestamp of message (ts)
     *
     * @return string|null
     */
    public function getTs(): ?string
    {
        return $this->ts;
    }

    /**
     * Add block on Slack message.
     *
     * @param SlackScope|array $block
     * @return void
     */
    public function addBlock(SlackScope|array $block): void
    {
        if ($block instanceof SlackScope) {
            $block = $block->getOptions();
        }
        $this->blocks[] = $block;
    }

    /**
     * Add text section on Slack message.
     *
     * @param string|null $text
     * @return void
     */
    public function addTextSection(?string $text): void
    {
        $this->addBlock([
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => $text,
            ],
        ]);
    }

    /**
     * Add image on Slack message.
     *
     * @param string $url
     * @param string $alt
     * @return void
     */
    public function addImage(?string $url, string $alt = ''): void
    {
        if (!$url) {
            return;
        }
        $this->addBlock([
            'type' => 'image',
            'image_url' => $url,
            'alt_text' => $alt,
        ]);
    }

    /**
     * Add file on Slack message.
     *
     * @param string $externalId
     * @return void
     */
    public function addFile(string $externalId): void
    {
        $this->addBlock([
            'type' => 'file',
            'external_id' => $externalId,
            'source' => 'remote',
        ]);
    }

    /**
     * Add header on Slack message.
     *
     * @param string|null $header
     * @return void
     */
    public function addHeader(?string $header): void
    {
        $this->addBlock([
            'type' => 'header',
            'text' => [
                'type' => 'plain_text',
                'text' => $header,
            ],
        ]);
    }

    /**
     * Add user mentions on Slack message.
     *
     * @param array $usernames
     * @return void
     */
    public function addMentions(array $usernames = []): void
    {
        $section = new SlackMessageContext();
        $mention = '';
        array_map(function ($username) use (&$mention) {
            $mention .= "<@$username> ";
        }, $usernames);
        if (!empty($mention)) {
            $section->addText($mention);
            $this->addBlock($section);
        }
    }

    /**
     * Add color notice on Slack message.
     *
     * @param string $color
     * @param string $title
     * @param string $text
     * @return void
     */
    public function addNotice(string $color, string $title, string $text = ''): void
    {
        $this->attachments[] = [
            'color' => $color,
            'fields' => [
                [
                    'title' => $title,
                    'value' => $text,
                    'short' => false,
                ],
            ],
        ];
    }

    /**
     * Strip links from the given text.
     *
     * @param string $text
     * @return string
     */
    private function stripLinks(string $text): string
    {
        return preg_replace('/\[(.*?)]\((.*?)\)/', '<$2|$1>', $text);
    }
}
