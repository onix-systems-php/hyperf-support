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
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Comment\CreateCommentDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Trello\Comment\UpdateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloCardNotFoundException;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use Symfony\Component\HttpFoundation\Response;

use function Hyperf\Collection\collect;

class TrelloCommentApiService extends TrelloApiService
{
    /**
     * Create a comment on a card.
     * https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-id-actions-comments-post
     *
     * @param CreateCommentDTO $createCommentDTO
     * @return Comment|null
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function create(string $source, CreateCommentDTO $createCommentDTO): ?Comment
    {
        if (!$createCommentDTO->text) {
            return null;
        }
        $url = "/cards/$createCommentDTO->card_id/actions/comments";
        try {
            $response = $this->client->post(
                $this->signUrl($source, $url),
                ['form_params' => ['text' => $createCommentDTO->text],]
            );
        } catch (Exception $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                throw new TrelloCardNotFoundException();
            }
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);
        $data = collect(json_decode($response->getBody()->getContents(), true))->only(['id', 'data'])->all();

        return new Comment(...$data);
    }

    /**
     * Update a comment on a card.
     * https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-id-actions-idaction-comments-put
     *
     * @param string $source
     * @param UpdateCommentDTO $updateCommentDTO
     * @return mixed
     * @throws GuzzleException
     * @throws TrelloCardNotFoundException
     * @throws TrelloException
     */
    public function update(string $source, UpdateCommentDTO $updateCommentDTO): mixed
    {
        if (!$updateCommentDTO->text) {
            return null;
        }
        $url = "/cards/$updateCommentDTO->card_id/actions/$updateCommentDTO->id/comments";
        try {
            $response = $this->client->put(
                $this->signUrl($source, $url),
                ['form_params' => ['text' => $updateCommentDTO->text],]
            );
        } catch (Exception $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                throw new TrelloCardNotFoundException();
            }
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Delete a comment on a card.
     * https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-id-actions-idaction-comments-delete
     *
     * @param string $source
     * @param string $cardId
     * @param string $id
     * @return bool
     * @throws GuzzleException
     * @throws TrelloCardNotFoundException
     * @throws TrelloException
     */
    public function delete(string $source, string $cardId, string $id): bool
    {
        try {
            $response = $this->client->delete($this->signUrl($source, "/cards/$cardId/actions/$id/comments"));
        } catch (Exception $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                throw new TrelloCardNotFoundException();
            }
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);

        return true;
    }
}
