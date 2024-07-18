<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Ticket;

use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use OnixSystemsPHP\HyperfActionsLog\Event\Action;
use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfSupport\Adapter\SupportAdapter;
use OnixSystemsPHP\HyperfSupport\Constant\Actions;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Contract\TicketDescriptionGeneratorContract;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\CreateCommentDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Tickets\UpdateTicketDTO;
use OnixSystemsPHP\HyperfSupport\Enum\TicketCreator;
use OnixSystemsPHP\HyperfSupport\Events\TicketStatusHasChanged;
use OnixSystemsPHP\HyperfSupport\Events\TicketUpdated;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Repository\TicketRepository;
use OnixSystemsPHP\HyperfSupport\Service\Comment\CreateCommentService;
use OnixSystemsPHP\HyperfSupport\Transport\Comment\CommentTrelloTransport;
use Psr\EventDispatcher\EventDispatcherInterface;

use function Hyperf\Support\now;

class UpdateTicketService
{
    public function __construct(
        private readonly ValidatorFactoryInterface $validatorFactory,
        private readonly TicketRepository $ticketRepository,
        private readonly SupportAdapter $supportAdapter,
        private readonly CreateCommentService $createCommentService,
        private readonly TicketDescriptionGeneratorContract $descriptionGenerator,
        private readonly SourceConfiguratorInterface $sourceConfigurator,
        private readonly CoreAuthenticatableProvider $coreAuthenticatableProvider,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ?CorePolicyGuard $policyGuard,
    ) {
    }

    /**
     * Update a ticket.
     *
     * @param int $id
     * @param UpdateTicketDTO $updateTicketDTO
     * @param bool $internalCall
     *
     * @return Ticket
     */
    public function run(int $id, UpdateTicketDTO $updateTicketDTO, bool $internalCall = false): Ticket
    {
        $ticket = $this->ticketRepository->getById($id, false, true);
        $this->validate($updateTicketDTO);

        $ticketData = array_merge(
            $updateTicketDTO->toArray(),
            [
                'modified_by' => $this->coreAuthenticatableProvider->user()?->getId(),
            ]
        );

        $this->ticketRepository->update($ticket, $ticketData);
        $this->policyGuard?->check('update', $ticket, ['internalCall' => $internalCall]);
        $this->ticketRepository->save($ticket);

        if ($this->shouldTicketBeCompleted($ticket)) {
            $this->completeTicket($ticket);
        }
        if ($this->descriptionGenerator->inTriggerLists($ticket->source, $ticket->custom_fields['status'])) {
            $this->createNewCommentOnNewTicketStatus($ticket);
            $this->eventDispatcher->dispatch(new TicketStatusHasChanged($ticket));
        }

        $this->eventDispatcher->dispatch(new TicketUpdated($ticket));
        $this->eventDispatcher->dispatch(new Action(Actions::UPDATE_TICKET, $ticket, $ticketData));
        $this->supportAdapter->run(Actions::UPDATE_TICKET, $ticket);

        return $ticket;
    }

    /**
     * @param UpdateTicketDTO $updateTicketDTO
     *
     * @return void
     */
    private function validate(UpdateTicketDTO $updateTicketDTO): void
    {
        $this->validatorFactory
            ->make(
                $updateTicketDTO->toArray(),
                [
                    'source' => 'required|string',
                    'title' => 'sometimes|required|min:5|max:255',
                    'content' => 'sometimes|required|min:20',
                    'custom_fields' => 'sometimes|array',
                    'page_url' => 'sometimes|required|url:https',
                    'files' => 'sometimes|array'
                ]
            )
            ->validate();
    }

    /**
     * Create new comment on new ticket status.
     *
     * @param Ticket $ticket
     *
     * @return void
     */
    private function createNewCommentOnNewTicketStatus(Ticket $ticket): void
    {
        $this->createCommentService->run(CreateCommentDTO::make([
            'ticket_id' => $ticket->id,
            'content' => "Ticket status has been changed to {$ticket->custom_fields['status']}",
            'creator_name' => TicketCreator::System->value,
        ]), [CommentTrelloTransport::class], true);
    }

    /**
     * Complete a ticket.
     *
     * @param Ticket $ticket
     *
     * @return void
     */
    private function completeTicket(Ticket $ticket): void
    {
        $this->ticketRepository->update($ticket, ['completed_at' => now()]);
        $this->ticketRepository->save($ticket);
    }

    /**
     * Check whether the ticket is completed or not.
     *
     * @param Ticket $ticket
     *
     * @return bool
     */
    private function shouldTicketBeCompleted(Ticket $ticket): bool
    {
        return !empty($ticket->custom_fields['status'])
            && $ticket->custom_fields['status'] === $this->sourceConfigurator->getApiConfig(
                $ticket->source,
                'ticket',
                'done_status'
            )
            && !$ticket->completed_at;
    }
}
