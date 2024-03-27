<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Builder;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Card\UpdateCardDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Cover\CreateCardCoverDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\CustomField\CreateCustomFieldDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Attachment;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Card;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloCardNotFoundException;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCardApiService;
use OnixSystemsPHP\HyperfSupport\Integration\Trello\TrelloCustomFieldApiService;

use function Hyperf\Support\make;

class TrelloCardBuilder
{
    private TrelloCardApiService $trelloCardService;
    private TrelloCustomFieldApiService $trelloCustomFieldService;
    private array $failedCustomFieldsText = [];

    public function __construct(private readonly string $cardId, private readonly string $source)
    {
        $this->trelloCardService = make(TrelloCardApiService::class);
        $this->trelloCustomFieldService = make(TrelloCustomFieldApiService::class);
    }

    /**
     * Add cover on card.
     *
     * @param CreateCardCoverDTO $cardCoverDTO
     * @return $this
     * @throws TrelloException
     * @throws GuzzleException
     */
    public function addCover(CreateCardCoverDTO $cardCoverDTO): self
    {
        $this->trelloCardService->update($this->source, $this->cardId, UpdateCardDTO::make([
            'cover' => $cardCoverDTO,
        ]));

        return $this;
    }

    /**
     * Add attachment on card.
     *
     * @param Attachment $attachment
     * @return $this
     * @throws TrelloCardNotFoundException
     * @throws TrelloException
     * @throws GuzzleException
     */
    public function addAttachment(Attachment $attachment): self
    {
        $this->trelloCardService->createAttachment($this->source, $this->cardId, $attachment);

        return $this;
    }

    /**
     * Add custom field on card.
     *
     * @param CustomField $customField
     * @return $this
     * @throws TrelloException
     * @throws GuzzleException
     */
    public function addCustomField(CreateCustomFieldDTO $createCustomFieldDTO): self
    {
        $result = $this->trelloCustomFieldService->create($this->source, $createCustomFieldDTO);
        if (is_null($result)) {
            $this->failedCustomFieldsText[] = sprintf('**%s: %s**', $createCustomFieldDTO->field_name, $createCustomFieldDTO->value);
        }

        return $this;
    }

    /**
     * Write custom fields as text to card when cannot add them as usual custom fields.
     *
     * @throws GuzzleException
     * @throws TrelloException
     * @throws TrelloCardNotFoundException
     */
    public function writeFailedCustomFieldToCard(Card $card): void
    {
        $this->trelloCardService->update($this->source, $card->id, UpdateCardDTO::make([
            'desc' => $card->desc . "\n\n" . implode("\n\n", $this->failedCustomFieldsText)
        ]));
    }
}
