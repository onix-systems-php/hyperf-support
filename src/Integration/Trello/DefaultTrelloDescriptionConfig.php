<?php

declare(strict_types=1);

namespace OnixSystemsPHP\HyperfSupport\Integration\Trello;

use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Integration\Contract\Trello\TrelloDescriptionConfigContract;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

class DefaultTrelloDescriptionConfig implements TrelloDescriptionConfigContract
{
    public function __construct(private readonly SourceConfiguratorInterface $sourceConfigurator)
    {
    }

    /**
     * @inheritDoc
     */
    public function cover(Ticket $ticket): string
    {
        return match ($ticket->custom_fields['type']) {
            'Feature Request' => 'sky',
            'Tweak' => 'blue',
            'Bug' => match ((int)$ticket->custom_fields['level']) {
                1 => 'lime',
                2 => 'purple',
                3 => 'orange',
                4 => 'red',
                default => 'black',
            },
            default => 'yellow',
        };
    }

    /**
     * @inheritDoc
     */
    public function getMentions(Ticket $ticket): array
    {
        $members = $this->sourceConfigurator->getApiConfig(
            $ticket->source,
            'integrations',
            'trello',
            'members'
        ) ?? [];

        return $members[$ticket->custom_fields['status']] ?? $members['default'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getTrelloList(Ticket $ticket): ?string
    {
        $columns = $this->sourceConfigurator->getApiConfig($ticket->source, 'integrations', 'trello', 'lists') ?? [];

        return $columns[$ticket->custom_fields['status']] ?? $columns['default'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function inTriggerLists(string $source, string $status): bool
    {
        $list = $this->sourceConfigurator->getApiConfig($source, 'integrations', 'trello', 'lists')[$status];

        return in_array(
            $list,
            $this->sourceConfigurator->getApiConfig($source, 'integrations', 'trello', 'trigger_lists')
        );
    }
}
