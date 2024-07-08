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
use OnixSystemsPHP\HyperfSupport\Service\Integration\TrelloService;
use Psr\Http\Message\ResponseInterface as Response;

#[Controller]
class TrelloController extends AbstractController
{
    public function __construct(
        private readonly TrelloService $trelloService,
        private readonly SourceConfiguratorInterface $sourceConfigurator,
    ) {
    }

    public function init(): Response
    {
        return $this->response->json([]);
    }

    public function webhook(): Response
    {
        $data = $this->request->getParsedBody();
        if (!empty($data['action']['data']['attachment'])) {
            return $this->response->json([]);
        }
        if (!empty($data['action']['memberCreator']['username']) &&
            $this->sourceConfigurator->isValidApiKey(
                'trello',
                $data['action']['memberCreator']['username']
            )
        ) {
            return $this->response->json([]);
        }
        $this->trelloService->handleWebhook($data);

        return $this->response->json([]);
    }
}
