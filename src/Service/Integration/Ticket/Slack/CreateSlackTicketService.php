<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Integration\Ticket\Slack;

use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Stringable\Str;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Integration\Contract\Slack\SlackDescriptionConfigContract;
use OnixSystemsPHP\HyperfSupport\Integration\Exceptions\Slack\SlackException;
use OnixSystemsPHP\HyperfSupport\Integration\Slack\SlackApiService;
use OnixSystemsPHP\HyperfSupport\Integration\Slack\SlackMessage;
use OnixSystemsPHP\HyperfSupport\Integration\Slack\SlackMessageContext;
use OnixSystemsPHP\HyperfSupport\Integration\Slack\SlackMessageSection;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

class CreateSlackTicketService
{
    public function __construct(
        private readonly SlackApiService $slack,
        private readonly SourceConfiguratorInterface $sourceConfigurator,
        private readonly SlackDescriptionConfigContract $slackDescriptionConfig,
    ) {
    }

    /**
     * Create a ticket on Slack.
     *
     * @param Ticket $ticket
     * @return Ticket
     * @throws GuzzleException
     * @throws SlackException
     */
    public function run(Ticket $ticket): Ticket
    {
        $message = new SlackMessage($ticket->source, $ticket->ticket_title);
        $message->setPlainText($ticket->ticket_title);

        if (!empty($ticket->trello_url)) {
            $context = new SlackMessageContext();
            $context->addText(sprintf("<%s|*Open Trello card*>", $ticket->trello_url));
            $message->addBlock($context);
        }

        $context = new SlackMessageContext();
        $context->addImage(
            $this->sourceConfigurator->getApiConfig($ticket->source, 'app', 'icon'),
            $this->sourceConfigurator->getApiConfig($ticket->source, 'app', 'name')
        );

        $section = new SlackMessageSection();
        foreach (
            $this->sourceConfigurator->getApiConfig(
                $ticket->source,
                'integrations',
                'slack',
                'custom_fields'
            ) as $name
        ) {
            $section->addText(
                sprintf(
                    "*%s:*\n%s",
                    ucfirst($name),
                    $ticket->custom_fields[$name]
                )
            );
        }
        $section->addText(sprintf("*Created by:*\n%s", $ticket->creator?->getUsername()));
        if ($this->getTextForModifier($ticket)) {
            $section->addText($this->getTextForModifier($ticket));
        }
        $message->addBlock($section);

        $section = new SlackMessageSection();
        $section->addText(sprintf("*When:*\n%s UTC", $ticket->created_at->format('m/d/Y h:i A')));
        if ($ticket->page_url && Str::isUrl($ticket->page_url)) {
            $section->addText(sprintf("*Page with an issue:*\n<%s|Open page>", $ticket->page_url));
        }
        $message->addBlock($section);

        $context = new SlackMessageContext();
        $context->addText(sprintf("*Ticket body:*\n%s", $ticket->content));
        $message->addBlock($context);

        $message->addNotice(
            $this->slackDescriptionConfig->color($ticket),
            $this->slackDescriptionConfig->label($ticket),
            $this->slackDescriptionConfig->description($ticket),
        );
        $message->addMentions($this->slackDescriptionConfig->getMentions($ticket));

        if ($ticket->slack_id) {
            $message->setTs($ticket->slack_id);
        }

        foreach ($ticket->files as $file) {
            $message->addImage($file->url, 'body_image');
        }

        $result = match (empty($message->getTs())) {
            true => $this->slack->postMessage($ticket->source, $message),
            false => $this->slack->updateMessage($ticket->source, $message),
        };
        if (!empty($result->ts)) {
            $ticket->slack_id = $result->ts;
            $ticket->save();
        }

        return $ticket;
    }

    /**
     * Get text for the ticket modifier.
     *
     * @param Ticket $ticket
     * @return string|null
     */
    public function getTextForModifier(Ticket $ticket): ?string
    {
        $format = match (true) {
            !empty($ticket->archiver) => sprintf("*Archived by:*\n%s", $ticket->archiver->getUsername()),
            !empty($ticket->editor) => sprintf("*Modified by:*\n%s", $ticket->editor->getUsername()),
            default => null,
        };

        return !is_null($format) ? $format : null;
    }
}
