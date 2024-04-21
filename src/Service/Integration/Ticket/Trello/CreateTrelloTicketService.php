<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Trello;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\Builder\TrelloCardBuilder;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Contract\TicketDescriptionGeneratorContract;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Card\CreateCardDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Cover\CreateCardCoverDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\CustomField\CreateCustomFieldDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Attachment;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\ProcessFiles;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloApiService;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCardApiService;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

class CreateTrelloTicketService
{
    use ProcessFiles;

    public function __construct(
        private readonly TrelloApiService $trello,
        private readonly TrelloCardApiService $trelloCardService,
        private readonly TicketDescriptionGeneratorContract $descriptionGenerator,
        private readonly SourceConfiguratorInterface $sourceConfigurator,
    ) {
    }

    /**
     * Create Ticket on Trello.
     *
     * @param Ticket $ticket
     * @return Ticket
     * @throws TrelloException
     * @throws GuzzleException
     */
    public function run(Ticket $ticket): Ticket
    {
        $createCardDTO = CreateCardDTO::make([
            'name' => $ticket->ticket_title,
            'desc' => $ticket->content,
            'pos' => 'top',
            'listName' => $this->descriptionGenerator->getTrelloList($ticket),
        ]);
        if (!empty($members = $this->descriptionGenerator->getMentionsByIntegration('trello', $ticket))) {
            if ($idMembers = $this->trello->getMembers($ticket->source)->getIdMembers($members)) {
                $createCardDTO->idMembers = $idMembers;
            }
        }

        $card = $this->trelloCardService->create($ticket->source, $createCardDTO);
        $this->trello->registerWebhook(
            $ticket->source,
            $card->id,
            $this->sourceConfigurator->getApiConfig($ticket->source, 'integrations', 'trello', 'webhook_url'),
        );
        $ticket->trello_id = $card->id;
        $trelloCardBuilder = new TrelloCardBuilder($card->id, $ticket->source);
        $ticket->trello_short_link = $card->shortLink;

        try {
            foreach (
                $this->sourceConfigurator->getApiConfig(
                    $ticket->source,
                    'integrations',
                    'trello',
                    'custom_fields'
                ) as $name
            ) {
                $trelloCardBuilder->addCustomField(
                    CreateCustomFieldDTO::make([
                        'card_id' => $card->id,
                        'field_name' => ucfirst($name),
                        'value' => $ticket->custom_fields[$name],
                    ])
                );
            }
            $trelloCardBuilder->addCover(
                CreateCardCoverDTO::make(['color' => $this->descriptionGenerator->cover($ticket)])
            );
            if (!empty($ticket->user)) {
                $ticketUrl = $this->sourceConfigurator->getApiConfig($ticket->source, 'app', 'domain');
                $trelloCardBuilder->addAttachment(new Attachment('Open Ticket', url: $ticketUrl));
            }
            if ($ticket->page_url) {
                $trelloCardBuilder->addAttachment(new Attachment('Page With an Issue', url: $ticket->page_url));
            }
            $trelloCardBuilder->writeFailedCustomFieldsToCard($card);
            $ticket = $this->processFiles($ticket);
        } catch (TrelloException $e) {
            $this->trelloCardService->delete($ticket->source, $card->id);
            throw $e;
        }
        $ticket->save();

        return $ticket;
    }
}
