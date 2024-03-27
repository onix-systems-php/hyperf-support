<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Integration\Trello;

use GuzzleHttp\Exception\GuzzleException;
use OnixSystemsPHP\HyperfSupport\Entity\Trello\Attachment;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Trello\TrelloException;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

trait ProcessFiles
{
    public function __construct(private readonly TrelloCardApiService $trelloCardService) {}

    /**
     * Process the files.
     *
     * @param Ticket|Comment $entity
     * @return Ticket|Comment
     * @throws TrelloException
     * @throws GuzzleException
     */
    protected function processFiles(Ticket|Comment $entity): Ticket|Comment
    {
        $trelloId = $entity->trello_id ?: $entity->ticket->trello_id;
        if (!$trelloId || empty($entity->files)) {
            return $entity;
        }
        if ($entity instanceof Ticket) {
            $card = $this->trelloCardService->getCard($entity->source, $trelloId);
        } else {
            $card = $this->trelloCardService->getCard($entity->ticket->source, $trelloId);
        }
        foreach ($entity->files as $file) {
            $cardAttachments = array_filter($card->attachments, fn($attachment) => $attachment->url === $file->url);
            if (!empty($cardAttachments)) {
                if ($entity instanceof Ticket) {
                    $this->trelloCardService->createAttachment($entity->source, $trelloId, new Attachment($file->name, $file->url));
                } else {
                    $this->trelloCardService->createAttachment($entity->ticket->source, $trelloId, new Attachment($file->name, $file->url));
                }
            }
        }

        return $entity;
    }
}
