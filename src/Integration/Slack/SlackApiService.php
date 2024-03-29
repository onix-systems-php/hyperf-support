<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Slack;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Entity\Slack\SlackResponse;
use OnixSystemsPHP\HyperfSupport\Entity\Slack\UserSlack;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Slack\SlackException;

use function Hyperf\Collection\collect;

class SlackApiService
{
    /** @var Client[] $clients */
    private array $clients;

    public function __construct(private readonly SourceConfiguratorInterface $sourceConfigurator) {}

    /**
     * Post the given message on Slack.
     * https://api.slack.com/methods/chat.postMessage
     *
     * @param string $source
     * @param SlackMessage $message
     * @return SlackResponse
     * @throws GuzzleException
     * @throws SlackException
     */
    public function postMessage(string $source, SlackMessage $message): SlackResponse
    {
        $client = $this->getClientInstance($source);
        $response = $client->post("chat.postMessage", [
            'json' => $message->getOptions(),
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->throwExceptionIfResponseIsNotOK($data);

        return new SlackResponse(...collect($data)->only(['ts', 'message'])->all());
    }

    /**
     * Update the given message on Slack.
     * https://api.slack.com/methods/chat.update
     *
     * @param string $source
     * @param SlackMessage $message
     * @return SlackResponse
     * @throws GuzzleException
     * @throws SlackException
     */
    public function updateMessage(string $source, SlackMessage $message): SlackResponse
    {
        $client = $this->getClientInstance($source);
        $response = $client->post("chat.update", [
            'json' => $message->getOptions(),
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->throwExceptionIfResponseIsNotOK($data);

        return new SlackResponse(...collect($data)->only(['ts', 'message'])->all());
    }

    /**
     * Add remote file on Slack.
     * https://api.slack.com/methods/files.remote.add
     *
     * @param string $source
     * @param string $title
     * @param string $externalUrl
     * @return string
     * @throws GuzzleException
     * @throws SlackException
     */
    public function addRemoteFile(string $source, string $title, string $externalUrl): string
    {
        $client = $this->getClientInstance($source);
        $params = [
            'external_id' => uniqid(),
            'title' => $title,
            'external_url' => $externalUrl,
        ];
        $response = $client->get('files.remote.add?' . http_build_query($params));
        $this->throwExceptionIfResponseIsNotOK(json_decode($response->getBody()->getContents(), true));

        return $params['external_id'];
    }

    /**
     * Delete message on slack by timestamp(ts).
     * https://api.slack.com/methods/chat.delete
     *
     * @param string $source
     * @param string $ts
     * @return bool
     * @throws SlackException|GuzzleException
     */
    public function delete(string $source, string $ts): bool
    {
        $client = $this->getClientInstance($source);
        $channelId = $this->sourceConfigurator->getApiConfig($source, 'integrations', 'slack', 'channel_id');
        $response = $client->post('chat.delete', [
            'json' => ['channel' => $channelId, 'ts' => $ts],
        ]);
        $this->throwExceptionIfResponseIsNotOK(json_decode($response->getBody()->getContents(), true));

        return true;
    }

    /**
     * Get user from Slack by id.
     * https://api.slack.com/methods/users.info
     *
     * @param string $source
     * @param string|null $userId
     * @return UserSlack|array
     * @throws GuzzleException
     */
    public function getUser(string $source, ?string $userId): ?UserSlack
    {
        $client = $this->getClientInstance($source);
        if (!$userId) {
            return null;
        }
        $response = $client->get("users.info?user=$userId");
        $data = json_decode($response->getBody()->getContents(), true);
        if (!$data['ok']) {
            return null;
        }

        return new UserSlack(...collect($data['user'])->only(['id', 'real_name'])->all());
    }

    /**
     * Return an instance of GuzzleHttp\Client for the given source.
     *
     * @param string $source
     * @return Client
     */
    public function getClientInstance(string $source): Client
    {
        if (empty($this->clients[$source])) {
            $this->clients[$source] = $this->createClient(
                $this->sourceConfigurator->getApiConfig($source, 'integrations', 'slack', 'token')
            );
        }

        return $this->clients[$source];
    }

    /**
     * Create an instance of GuzzleHttp\Client.
     *
     * @param string $token
     * @return Client
     */
    private function createClient(string $token): Client
    {
        if (empty($token)) {
            throw new BusinessException(message: 'Slack Channel is not defined.');
        }

        return new Client([
            'base_uri' => 'https://slack.com/api/',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }

    /**
     * Throw exception if the give response is not ok.
     *
     * @param array $data
     * @return void
     * @throws SlackException
     */
    private function throwExceptionIfResponseIsNotOK(array $data): void
    {
        if (!$data['ok']) {
            $errorMessage = implode(' | ', $data['errors'] ?? [$data['error']]);
            throw  new SlackException($errorMessage);
        }
    }
}
