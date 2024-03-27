<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Trello;

use FriendsOfHyperf\Macros\Exception\ItemNotFoundException;
use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Card\UpdateCardDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\CustomField\UpdateCustomFieldDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Options\Option;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\ProcessFiles;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCardService;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCustomFieldService;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloService;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

readonly class UpdateTrelloTicketService
{
    use ProcessFiles;

    public function __construct(
        private TrelloCardService $trelloCardService,
        private TrelloCustomFieldService $trelloCustomField,
        private TrelloService $trello,
        private SourceConfiguratorInterface $sourceConfigurator,
    ) {}

    /**
     * Update ticket on Trello.
     *
     * @param Ticket $ticket
     * @return Ticket
     * @throws TrelloException
     * @throws GuzzleException
     */
    public function run(Ticket $ticket): Ticket
    {
        if ($ticket->trello_id) {
            try {
                $this->trelloCardService->update($ticket->source, $ticket->trello_id, UpdateCardDTO::make([
                    'name' => $ticket->ticket_title,
                    'desc' => $ticket->content,
                    'pos' => 'top',
                    'idList' => $this->trello->getBoard($ticket->source)->getListIdByName(
                        $this->sourceConfigurator->getApiConfig(
                            $ticket->source,
                            'trello',
                            'lists',
                            $ticket->custom_fields['status'],
                        )
                    ),
                ]));
                foreach ($ticket->custom_fields as $name => $value) {
                    $customField = $this->trelloCustomField->getCustomFieldByName($ticket->source, ucfirst($name));
                    if (!is_null($customField)) {
                        /** @var Option $option */
                        $option = current(
                            array_filter($customField->options, fn(Option $option) => $value === $option->value->text)
                        );
                        $this->trelloCardService->updateCustomField($ticket->source, UpdateCustomFieldDTO::make([
                            'card_id' => $ticket->trello_id,
                            'field_id' => $customField->id,
                            'option_id' => $option->id
                        ]));
                    }
                }
            } catch (ItemNotFoundException $e) {
                throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
            }

            return $this->processFiles($ticket);
        }
        return $ticket;
    }
}
