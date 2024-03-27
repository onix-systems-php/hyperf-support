<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Trello;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\CustomField\CreateCustomFieldDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\CustomField\UpdateCustomFieldDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\CustomField;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Options\Option;
use OnixSystemsPHP\HyperfSupport\Enum\Trello\ModelType;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloCardNotFoundException;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use Symfony\Component\HttpFoundation\Response;

class TrelloCustomFieldApiService extends TrelloApiService
{
    /**
     * Create trello custom field on a board.
     * https://developer.atlassian.com/cloud/trello/rest/api-group-customfields/#api-customfields-post
     *
     * @param string $source
     * @param CreateCustomFieldDTO $customFieldDTO
     * @return array|null
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function create(string $source, CreateCustomFieldDTO $customFieldDTO): ?array
    {
        $customField = $this->getCustomFieldByName($source, $customFieldDTO->field_name);
        $options = array_filter($customField?->options ?? [], fn($option) => $option->value === $customFieldDTO->value);
        /** @var Option|null $option */
        $option = current($options) ?: null;

        return $this->update($source, UpdateCustomFieldDTO::make([
            'cardId' => $customFieldDTO->card_id,
            'fieldId' => $option?->customFieldId,
            'optionId' => $option?->id,
        ]));
    }

    /**
     * Update a custom field on a board. https://developer.atlassian.com/cloud/trello/rest/api-group-customfields/#api-customfields-id-put
     *
     * @param string $cardId
     * @param string|null $fieldId
     * @param string|null $optionId
     * @return array|null
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function update(string $source, UpdateCustomFieldDTO $updateCustomFieldDTO): ?array
    {
        if (!$updateCustomFieldDTO->fieldId || !$updateCustomFieldDTO->optionId) {
            return null;
        }
        $url = "/cards/$updateCustomFieldDTO->cardId/customField/$updateCustomFieldDTO->fieldId/item";
        try {
            $response = $this->client->put($this->signUrl($source, $url), [
                    'form_params' => [
                        'idModel' => $updateCustomFieldDTO->cardId,
                        'idCustomField' => $updateCustomFieldDTO->fieldId,
                        'idValue' => $updateCustomFieldDTO->optionId,
                        'modelType' => ModelType::Card->value,
                    ],
                ]
            );
        } catch (Exception $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                throw new TrelloCardNotFoundException();
            }
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get custom field by its name.
     *
     * @param string $source
     * @param string $name
     * @return CustomField|null
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function getCustomFieldByName(string $source, string $name): ?CustomField
    {
        $customFields = array_filter(
            $this->getCustomFields($source),
            fn(CustomField $customField) => $customField->name === $name
        );

        return current($customFields) ?: null;
    }

    /**
     * Get custom fields from the active board. https://developer.atlassian.com/cloud/trello/rest/api-group-boards/#api-boards-id-customfields-get
     *
     * @param string $source
     * @return array
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function getCustomFields(string $source): array
    {
        try {
            $response = $this->client->get(
                $this->signUrl($source, "/boards/{$this->getBoard($source)->id}/customFields")
            );
        } catch (Exception $e) {
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);
        $data = json_decode($response->getBody()->getContents(), true);

        return array_map(fn($item) => new CustomField($item['id'], $item['name'], $item['options']), $data);
    }
}
