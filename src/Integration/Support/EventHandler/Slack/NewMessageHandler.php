<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Support\EventHandler\Slack;

use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatable;
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

readonly class NewMessageHandler implements EventHandlerInterface
{
    public function __construct(
        private AddExternalFileService $addExternalFileService,
        private CreateCommentService $createCommentService,
        private ?CoreAuthenticatable $coreAuthenticatable
    ) {}

    /**
     * @inheritDoc
     */
    public function handle(Event $event, Ticket|Comment $entity): void
    {
        /** @var SourceConfiguratorInterface $sourceConfigurator */
        $sourceConfigurator = make(SourceConfiguratorInterface::class);
        $bearer = 'Bearer ' . $sourceConfigurator->getApiConfig($entity->source, 'slack', 'token');
        $files = collect($event->getFileLinks())->map(
            fn($file) => $this->addExternalFileService->run($file['url_private_download'], $this->coreAuthenticatable, [
                'headers' => ['Authorization' => $bearer],
            ])
        );
        $this->createCommentService->run(CreateCommentDTO::make([
            'ticket_id' => $entity->id,
            'content' => $event->getText(),
            'creator_name' => $event->getUsername(),
            'slack_comment_id' => $event->getEventIdentifier(),
            'files' => $files->map(fn($file) => ['id' => $file->id])->all(),
        ]), [CommentSlackTransport::class]);
    }
}
