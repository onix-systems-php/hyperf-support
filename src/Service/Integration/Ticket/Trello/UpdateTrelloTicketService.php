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
use OnixSystemsPHP\HyperfSupport\Builder\TrelloCardBuilder;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Card\UpdateCardDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\CustomField\UpdateCustomFieldDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Card;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Options\Option;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloCardNotFoundException;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\ProcessFiles;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloApiService;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCardApiService;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCustomFieldApiService;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

class UpdateTrelloTicketService
{
    use ProcessFiles;

    public function __construct(
        private readonly TrelloCardApiService $trelloCardService,
        private readonly TrelloCustomFieldApiService $trelloCustomField,
        private readonly TrelloApiService $trello,
        private readonly SourceConfiguratorInterface $sourceConfigurator,
    ) {
    }

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
                            'integrations',
                            'trello',
                            'lists',
                            $ticket->custom_fields['status'],
                        )
                    ),
                ]));
                $this->updateCustomFields($ticket);
            } catch (ItemNotFoundException $e) {
                throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
            }

            return $this->processFiles($ticket);
        }
        return $ticket;
    }

    /**
     * Update custom fields.
     *
     * @param Ticket $ticket
     * @return void
     * @throws GuzzleException
     * @throws TrelloException
     * @throws TrelloCardNotFoundException
     */
    private function updateCustomFields(Ticket $ticket): void
    {
        $trelloCardBuilder = new TrelloCardBuilder($ticket->trello_id, $ticket->source);
        foreach ($ticket->custom_fields as $name => $value) {
            $customField = $this->trelloCustomField->getCustomFieldByName($ticket->source, ucfirst($name));
            if (!is_null($customField)) {
                /** @var Option $option */
                $option = current(
                    array_filter($customField->options, fn(Option $option) => $value === $option->value->text)
                ) ?: null;
                $this->trelloCardService->updateCustomFieldOnCard($ticket->source, UpdateCustomFieldDTO::make([
                    'cardId' => $ticket->trello_id,
                    'fieldId' => $customField->id,
                    'optionId' => $option?->id,
                ]));
            } else {
                $trelloCardBuilder->addFailedCustomField($name, $value);
            }
        }
        $trelloCardBuilder->writeFailedCustomFieldsToCard(new Card($ticket->trello_id, $ticket->content));
    }
}
