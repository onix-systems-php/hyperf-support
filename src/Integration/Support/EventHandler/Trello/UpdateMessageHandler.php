<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Trello;

use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use OnixSystemsPHP\HyperfFileUpload\Service\AddExternalFileService;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\UpdateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Event;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\EventHandlerInterface;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Comment\UpdateCommentService;
use OnixSystemsPHP\HyperfSupport\Transport\Comment\CommentTrelloTransport;

use function Hyperf\Collection\collect;
use function Hyperf\Support\make;

readonly class UpdateMessageHandler implements EventHandlerInterface
{
    public function __construct(
        private AddExternalFileService $addExternalFileService,
        private UpdateCommentService $updateCommentService,
        private ?CoreAuthenticatableProvider $userProvider
    ) {}

    /**
     * @inheritDoc
     */
    public function handle(Event $event, Ticket|Comment $entity): void
    {
        /** @var SourceConfiguratorInterface $sourceConfigurator */
        $sourceConfigurator = make(SourceConfiguratorInterface::class);
        $key = $sourceConfigurator->getApiConfig($entity->ticket->source, 'integrations', 'trello', 'key');
        $token = $sourceConfigurator->getApiConfig($entity->ticket->source, 'integrations', 'trello', 'token');

        $files = collect($event->getFileLinks())->map(
            fn($link) => $this->addExternalFileService->run($link, $this->userProvider?->user(), [
                'headers' => [
                    'Authorization' => "OAuth oauth_consumer_key=\"$key\", oauth_token=\"$token\""
                ]
            ])
        );
        $this->updateCommentService->run($entity->id, UpdateCommentDTO::make([
            'content' => $event->getText(),
            'files' => $files->map(fn($file) => ['id' => $file->id])->all(),
        ]), [CommentTrelloTransport::class]);
    }
}
