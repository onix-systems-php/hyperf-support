<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Trello;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Contract\TicketDescriptionGeneratorBase;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Board;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Member\Members;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use Symfony\Component\HttpFoundation\Response;

class TrelloApiService
{
    protected const API_URL = 'https://api.trello.com/1';
    protected Client $client;
    protected array $boards;

    /**
     * @throws GuzzleException
     */
    public function __construct(
        protected readonly SourceConfiguratorInterface $sourceConfigurator,
        protected readonly TicketDescriptionGeneratorBase $descriptionGenerator
    ) {
        $this->client = new Client(['base_uri' => self::API_URL, 'headers' => ['Accept' => 'application/json']]);
    }

    /**
     * Get active board for the given source.
     *
     * @param string $source
     * @return Board
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function getBoard(string $source): Board
    {
        if (empty($this->boards[$source])) {
            $this->boards[$source] = $this->setBoard($source);
        }

        return $this->boards[$source];
    }

    /**
     * Create a webhook for the given source.
     * https://developer.atlassian.com/cloud/trello/rest/api-group-webhooks/#api-webhooks-post
     *
     * @param string $source
     * @param string $modelId
     * @param string $callbackUrl
     * @return true
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function registerWebhook(string $source, string $modelId, string $callbackUrl): bool
    {
        try {
            $response = $this->client->post($this->signUrl($source, '/webhooks'), [
                'form_params' => [
                    'callbackURL' => $callbackUrl,
                    'idModel' => $modelId,
                ],
            ]);
        } catch (Exception $e) {
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);

        return true;
    }

    /**
     * Get members.
     *
     * @param string $source
     * @return Members
     * @throws GuzzleException
     * @throws TrelloException
     */
    public function getMembers(string $source): Members
    {
        $board = $this->getBoard($source);
        try {
            $response = $this->client->get($this->signUrl($source, "/boards/$board->id/members"));
        } catch (Exception $e) {
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);

        return new Members(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * Sign an url.
     *
     * @param string $source
     * @param string $url
     * @param array $options
     * @return string
     */
    protected function signUrl(string $source, string $url, array $options = []): string
    {
        $key = $this->sourceConfigurator->getApiConfig($source, 'trello', 'key');
        $token = $this->sourceConfigurator->getApiConfig($source, 'trello', 'token');

        return self::API_URL . $url . '?' . http_build_query(array_merge(['key' => $key, 'token' => $token], $options));
    }

    /**
     * Throw an exception if response is not 200.
     *
     * @param object $response
     * @return void
     * @throws TrelloException
     */
    protected function throwExceptionIfResponseIsNotOK(object $response): void
    {
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new TrelloException($response->getReasonPhrase());
        }
    }

    /**
     * Set board.
     *
     * @throws GuzzleException
     * @throws TrelloException
     */
    private function setBoard(string $source): Board
    {
        $boardName = $this->sourceConfigurator->getApiConfig($source, 'trello', 'boardName');
        $token = $this->sourceConfigurator->getApiConfig($source, 'trello', 'token');
        $key = $this->sourceConfigurator->getApiConfig($source, 'trello', 'key');
        if (!$key || !$token) {
            throw new BusinessException(message: 'Trello API key is not set.');
        }
        if (!$boardName) {
            throw new TrelloException('Trello board name is not set.');
        }
        try {
            $response = $this->client->get($this->signUrl($source, '/members/me/boards', [
                'organizations' => true,
                'lists' => 'open',
            ]));
        } catch (Exception $e) {
            throw new TrelloException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        $this->throwExceptionIfResponseIsNotOK($response);
        $data = json_decode($response->getBody()->getContents(), true);
        $boardData = current(array_filter($data, fn($board) => $board['name'] === $boardName));
        if (!$boardData) {
            throw new TrelloException("Board with name $boardName not found.");
        }

        return new Board(
            $boardData['id'],
            $boardData['name'],
            $boardData['desc'],
            $boardData['url'],
            $boardData['shortLink'],
            $boardData['shortUrl'],
            $boardData['lists'],
        );
    }
}
