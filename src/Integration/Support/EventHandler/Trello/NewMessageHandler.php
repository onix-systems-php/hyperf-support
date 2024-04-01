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
use OnixSystemsPHP\HyperfSupport\DTO\Comments\CreateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Event;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\EventHandlerInterface;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Comment\CreateCommentService;
use OnixSystemsPHP\HyperfSupport\Transport\Comment\CommentTrelloTransport;

use function Hyperf\Collection\collect;
use function Hyperf\Support\make;

readonly class NewMessageHandler implements EventHandlerInterface
{
    public function __construct(
        private AddExternalFileService $addExternalFileService,
        private CreateCommentService $createCommentService,
        private ?CoreAuthenticatableProvider $userProvider,
    ) {}

    /**
     * @inheritDoc
     */
    public function handle(Event $event, Ticket|Comment $entity): void
    {
        /** @var SourceConfiguratorInterface $sourceConfigurator */
        $sourceConfigurator = make(SourceConfiguratorInterface::class);
        $key = $sourceConfigurator->getApiConfig($entity->source, 'integrations', 'trello', 'key');
        $token = $sourceConfigurator->getApiConfig($entity->source, 'integrations', 'trello', 'token');

        $files = collect($event->getFileLinks())->map(
            fn($link) => $this->addExternalFileService->run($link, $this->userProvider->user(), [
                'headers' => [
                    'Authorization' => "OAuth oauth_consumer_key=\"$key\", oauth_token=\"$token\""
                ]
            ])
        );
        $this->createCommentService->run(CreateCommentDTO::make([
            'from' => 'slack',
            'source' => $entity->source,
            'ticket_id' => $entity->id,
            'content' => $event->getText(),
            'creator_name' => $event->getUsername(),
            'trello_comment_id' => $event->getEventIdentifier(),
            'files' => $files->map(fn($file) => ['id' => $file->id])->all(),
        ]), [CommentTrelloTransport::class]);
    }
}
