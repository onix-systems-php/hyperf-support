<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Controller\Webhooks;

use Hyperf\HttpServer\Annotation\Controller;
use OnixSystemsPHP\HyperfCore\Controller\AbstractController;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Service\Integration\SlackService;
use Psr\Http\Message\ResponseInterface as Response;

#[Controller]
class SlackController extends AbstractController
{
    public function __construct(
        private readonly SlackService $slackService,
        private readonly SourceConfiguratorInterface $sourceConfigurator,
    ) {}

    public function webhook(): Response
    {
        $data = $this->request->getParsedBody();
        if (!empty($data['challenge'])) {
            return $this->response->json($data['challenge']);
        }
        $event = $data['event'];
        $channelId = $event['channel'];
        if (!empty($channelId) && !$this->sourceConfigurator->isValidApiKey('slack', $channelId)) {
            return $this->response->json([]);
        }
        if (!empty($event['bot_profile']) || !empty($event['message']['bot_profile'])) {
            return $this->response->json([]);
        }
        if (!empty($event['client_msg_id']) || !empty($event['message']['client_msg_id']) || !empty($event['subtype'])) {
            $this->slackService->handleWebhook($data);
        }
        return $this->response->json([]);
    }
}
