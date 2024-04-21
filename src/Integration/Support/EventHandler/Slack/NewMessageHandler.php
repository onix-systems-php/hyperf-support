<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Slack;

use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use OnixSystemsPHP\HyperfFileUpload\Service\AddExternalFileService;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\CreateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Entity\Event;
use OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\EventHandlerInterface;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Service\Comment\CreateCommentService;
use OnixSystemsPHP\HyperfSupport\Transport\Comment\CommentSlackTransport;

use function Hyperf\Collection\collect;
use function Hyperf\Support\make;

class NewMessageHandler implements EventHandlerInterface
{
    public function __construct(
        private readonly AddExternalFileService $addExternalFileService,
        private readonly CreateCommentService $createCommentService,
        private readonly ?CoreAuthenticatableProvider $user
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handle(Event $event, Ticket|Comment $entity): void
    {
        /** @var SourceConfiguratorInterface $sourceConfigurator */
        $sourceConfigurator = make(SourceConfiguratorInterface::class);
        $bearer = 'Bearer ' . $sourceConfigurator->getApiConfig($entity->source, 'integrations', 'slack', 'token');
        $files = collect($event->getFileLinks())->map(
            fn($file) => $this->addExternalFileService->run($file['url_private_download'], $this->user, [
                'headers' => ['Authorization' => $bearer],
            ])
        );
        $this->createCommentService->run(CreateCommentDTO::make([
            'from' => 'slack',
            'source' => $entity->source,
            'ticket_id' => $entity->id,
            'content' => $event->getText(),
            'creator_name' => $event->getUsername(),
            'slack_comment_id' => $event->getEventIdentifier(),
            'files' => $files->map(fn($file) => ['id' => $file->id])->all(),
        ]), [CommentSlackTransport::class]);
    }
}
