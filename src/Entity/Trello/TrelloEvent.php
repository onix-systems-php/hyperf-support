<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Trello;

use OnixSystemsPHP\HyperfSupport\Entity\Event;
use OnixSystemsPHP\HyperfSupport\Enum\Trello\TrelloActionType;

class TrelloEvent extends Event
{
    public const TRELLO_UNICODE = " \"\u{200C}\"";

    public array $fileLinks;

    public function __construct(
        public ?TrelloActionType $type,
        public string $commentId,
        public string $cardId,
        public string $creatorName,
        public ?string $updatedTicketStatus,
        public string $text = '',
    ) {}

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->text = $this->stripLinksFromText($this->text);
    }

    /**
     * @inheritDoc
     */
    public function getEventIdentifier(): string|int
    {
        return $this->commentId;
    }

    /**
     * @inheritDoc
     */
    public function getFileLinks(): array
    {
        return $this->fileLinks = $this->getFileLinksFromText($this->text);
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->creatorName;
    }

    /**
     * @inheritDoc
     */
    public function getTicketStatus(): ?string
    {
        return $this->updatedTicketStatus;
    }

    /**
     * Parse files from the given text.
     *
     * @param string $text
     * @return array
     */
    private function getFileLinksFromText(string $text): array
    {
        preg_match_all('#\[(.*)]\((.*)\)#', $text, $matches);

        return array_map(fn($link) => str_replace(self::TRELLO_UNICODE, '', $link), $matches[2]);
    }

    /**
     * Strip links from the given text.
     *
     * @param string $text
     * @return string
     */
    private function stripLinksFromText(string $text): string
    {
        $pattern = '#\[(.*?)]\((.*?)\)|!\[(.*?)]\((.*?)\)#';
        while (preg_match($pattern, $text)) {
            $text = preg_replace($pattern, '', $text);
        }

        return $text;
    }
}
