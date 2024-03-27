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
use OnixSystemsPHP\HyperfSupport\Contract\TicketDescriptionGeneratorBase;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Card\CreateCardDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Cover\CreateCardCoverDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\CustomField\CreateCustomFieldDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Attachment;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\ProcessFiles;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCardService;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloService;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

readonly class CreateTrelloTicketService
{
    use ProcessFiles;

    public function __construct(
        private TrelloService $trello,
        private TrelloCardService $trelloCardService,
        private TicketDescriptionGeneratorBase $descriptionGenerator,
        private SourceConfiguratorInterface $sourceConfigurator,
    ) {}

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
        ]);

        if (!empty($members = $this->descriptionGenerator->trelloMembers($ticket))) {
            if ($idMembers = $this->trello->getMembers($ticket->source)->getIdMembers($members)) {
                $createCardDTO->idMembers = $idMembers;
            }
        }

        $card = $this->trelloCardService->create(
            $ticket->source,
            $createCardDTO,
            $this->descriptionGenerator->trelloLists($ticket)
        );
        $this->trello->registerWebhook(
            $ticket->source,
            $card->id,
            $this->sourceConfigurator->getApiConfig($ticket->source, 'app', 'domain') . '/support/trello/webhook'
        );
        $ticket->trello_id = $card->id;
        $trelloCardBuilder = new TrelloCardBuilder($card->id, $ticket->source);
        $ticket->trello_short_link = $card->shortLink;

        try {
            foreach ($this->sourceConfigurator->getApiConfig($ticket->source, 'trello', 'customFields') as $name) {
                $trelloCardBuilder->addCustomField(
                    CreateCustomFieldDTO::make([
                        'card_id' => $card->id,
                        'field_name' => ucfirst($name),
                        'value' => $ticket->custom_fields[$name],
                    ])
                );
            }
            $trelloCardBuilder->addCover(CreateCardCoverDTO::make(['color' => $this->descriptionGenerator->cover($ticket)]));
            if (!empty($ticket->user)) {
                $ticketUrl = $this->sourceConfigurator->getApiConfig($ticket->source, 'app', 'domain');
                $trelloCardBuilder->addAttachment(new Attachment('Open Ticket', url: $ticketUrl));
            }
            if ($ticket->page_url) {
                $trelloCardBuilder->addAttachment(new Attachment('Page With an Issue', url: $ticket->page_url));
            }
            if (!empty($trelloCardBuilder->customFieldToString)) {
                $trelloCardBuilder->writeFailedCustomFieldToCard($card);
            }
            $ticket = $this->processFiles($ticket);
        } catch (TrelloException $e) {
            $this->trelloCardService->delete($ticket->source, $card->id);
            throw $e;
        }
        $ticket->save();

        return $ticket;
    }
}
