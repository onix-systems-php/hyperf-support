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
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Card\CreateCardDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Card\UpdateCardDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\CustomField\UpdateCustomFieldDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Attachment;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Card;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloCardNotFoundException;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use Symfony\Component\HttpFoundation\Response;

class TrelloCardApiService extends TrelloApiService
{
    /**
     * Create a card.
     * https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-post
     *
     * @param string $source
     * @param CreateCardDTO $cardDTO
     * @param string $listName
     * @return Card
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function create(string $source, CreateCardDTO $cardDTO): Card
    {
        if (!$cardDTO->listName) {
            throw  new TrelloException('List name cannot be empty.');
        }
        if (empty($listId = $this->getBoard($source)->getListIdByName($cardDTO->listName))) {
            throw new TrelloException("List with name `$cardDTO->listName` not found.");
        }
        $cardDTO->idList = $listId;
        try {
            $response = $this->client->post($this->signUrl($source, '/cards'), [
                'form_params' => $cardDTO->toArray(),
            ]);
        } catch (Exception $e) {
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);
        $data = json_decode($response->getBody()->getContents(), true);

        return new Card(
            $data['id'],
            $data['desc'],
            $data['shortLink'],
            $data['shortUrl'],
            $data['attachments'],
        );
    }

    /**
     * Update a card.
     * https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-id-put
     *
     * @param string $source
     * @param string $id
     * @param UpdateCardDTO $cardDTO
     * @return Card|null
     * @throws GuzzleException
     * @throws TrelloCardNotFoundException
     * @throws TrelloException
     */
    public function update(string $source, string $id, UpdateCardDTO $cardDTO): ?Card
    {
        try {
            $response = $this->client->put($this->signUrl($source, "/cards/$id"), [
                'form_params' => $cardDTO->toArray(),
            ]);
        } catch (Exception $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                throw new TrelloCardNotFoundException();
            }
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);

        $data = json_decode($response->getBody()->getContents(), true);

        return new Card(
            $data['id'],
            $data['desc'],
            $data['shortLink'],
            $data['shortUrl'],
        );
    }

    /**
     * Archive a card.
     *
     * @param string $source
     * @param string $id
     * @return bool
     * @throws GuzzleException
     * @throws TrelloCardNotFoundException
     * @throws TrelloException
     */
    public function archive(string $source, string $id): bool
    {
        $this->update($source, $id, UpdateCardDTO::make(['closed' => true]));

        return true;
    }

    /**
     * Delete a card.
     * https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-id-delete
     *
     * @param string $source
     * @param string $id
     * @return bool
     * @throws GuzzleException
     * @throws TrelloCardNotFoundException
     * @throws TrelloException
     */
    public function delete(string $source, string $id): bool
    {
        try {
            $response = $this->client->delete($this->signUrl($source, "/cards/$id"));
        } catch (Exception $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                throw new TrelloCardNotFoundException();
            }
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);

        return true;
    }

    /**
     * Create a link.
     * https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-id-attachments-post
     *
     * @param string $source
     * @param string $id
     * @param Attachment $attachment
     * @return bool
     * @throws GuzzleException
     * @throws TrelloCardNotFoundException
     * @throws TrelloException
     */
    public function createAttachment(string $source, string $id, Attachment $attachment): bool
    {
        try {
            $response = $this->client->post($this->signUrl($source, "/cards/$id/attachments"), [
                'form_params' => [
                    'name' => $attachment->name ?? 'Attachment',
                    'url' => $attachment->url,
                ],
            ]);
        } catch (Exception $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                throw new TrelloCardNotFoundException();
            }
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);

        return true;
    }

    /**
     * Get trello card by id.
     * https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-id-get
     *
     * @param string $source
     * @param string $id
     * @return Card
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function getCard(string $source, string $id): Card
    {
        try {
            $response = $this->client->get($this->signUrl($source, "/cards/$id", ['attachments' => 'true']));
        } catch (Exception $e) {
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);

        $data = json_decode($response->getBody()->getContents(), true);

        return new Card(
            $data['id'],
            $data['desc'],
            $data['shortLink'],
            $data['shortUrl'],
            $data['attachments'],
        );
    }

    /**
     * Update custom field on the card.
     *
     * @param string $source
     * @param UpdateCustomFieldDTO $updateCustomFieldDTO
     * @return mixed
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function updateCustomFieldOnCard(string $source, UpdateCustomFieldDTO $updateCustomFieldDTO): array
    {
        $url = "/cards/$updateCustomFieldDTO->cardId/customField/$updateCustomFieldDTO->fieldId/item";
        try {
            $response = $this->client->put($this->signUrl($source, $url), [
                'form_params' => [
                    'idValue' => $updateCustomFieldDTO->optionId,
                ],
            ]);
        } catch (Exception $e) {
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);

        return json_decode($response->getBody()->getContents(), true);
    }
}
