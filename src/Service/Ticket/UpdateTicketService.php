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
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfSupport\Adapter\SupportAdapter;
use OnixSystemsPHP\HyperfSupport\Constant\Actions;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Contract\TicketDescriptionGeneratorContract;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\CreateCommentDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Tickets\UpdateTicketDTO;
use OnixSystemsPHP\HyperfSupport\Enum\TicketCreator;
use OnixSystemsPHP\HyperfSupport\Events\TicketStatusHasChanged;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Repository\TicketRepository;
use OnixSystemsPHP\HyperfSupport\Service\Comment\CreateCommentService;
use OnixSystemsPHP\HyperfSupport\Transport\Comment\CommentTrelloTransport;
use Psr\EventDispatcher\EventDispatcherInterface;
use OpenApi\Attributes as OA;

use function Hyperf\Support\now;

#[OA\Schema(
    schema: 'UpdateTicketRequest',
    properties: [
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'content', type: 'string'),
        new OA\Property(property: 'source', type: 'string'),
        new OA\Property(property: 'custom_fields', type: 'object', example: '{"type": "Feature Request"}'),
        new OA\Property(property: 'page_url', type: 'string'),
        new OA\Property(property: 'modified_by', type: 'integer'),
    ],
    type: 'object',
)]
readonly class UpdateTicketService
{
    public function __construct(
        private ValidatorFactoryInterface $validatorFactory,
        private TicketRepository $ticketRepository,
        private ?CorePolicyGuard $policyGuard,
        private SupportAdapter $supportAdapter,
        private EventDispatcherInterface $eventDispatcher,
        private CreateCommentService $createCommentService,
        private TicketDescriptionGeneratorContract $descriptionGenerator,
        private SourceConfiguratorInterface $sourceConfigurator,
    ) {}

    /**
     * Update a ticket.
     *
     * @param int $id
     * @param UpdateTicketDTO $updateTicketDTO
     * @return Ticket|null
     */
    public function run(int $id, UpdateTicketDTO $updateTicketDTO): ?Ticket
    {
        $ticket = $this->ticketRepository->findById($id);
        $this->validate($updateTicketDTO);

        $this->policyGuard?->check('update', $ticket);
        $this->ticketRepository->update($ticket, $updateTicketDTO->toArray());
        $this->ticketRepository->save($ticket);

        if ($this->shouldTicketBeCompleted($ticket)) {
            $this->completeTicket($ticket);
        }
        if ($this->descriptionGenerator->inTriggerLists($ticket->source, $ticket->custom_fields['status'])) {
            $this->createNewCommentOnNewTicketStatus($ticket);
            $this->eventDispatcher->dispatch(new TicketStatusHasChanged($ticket));
        }

        $this->eventDispatcher->dispatch(new Action(Actions::UPDATE_TICKET, $ticket, $updateTicketDTO->toArray()));
        $this->supportAdapter->run(Actions::UPDATE_TICKET, $ticket);

        return $ticket;
    }

    /**
     * @param UpdateTicketDTO $updateTicketDTO
     * @return void
     */
    public function validate(UpdateTicketDTO $updateTicketDTO): void
    {
        $this->validatorFactory->make($updateTicketDTO->toArray(), ['source' => 'required'])->validate();

        $validationRules = [
            'title' => ['string', 'min:5', 'max:255'],
            'content' => ['string', 'min:20'],
            'custom_fields' => ['array'],
            'page_url' => ['url:https'],
            'modified_by' => ['integer'],
        ];

        $this->validatorFactory->make($updateTicketDTO->toArray(), $validationRules)->validate();
    }

    /**
     * Create new comment on new ticket status.
     *
     * @param Ticket $ticket
     * @return void
     */
    private function createNewCommentOnNewTicketStatus(Ticket $ticket): void
    {
        $this->createCommentService->run(CreateCommentDTO::make([
            'ticket_id' => $ticket->id,
            'content' => "Ticket status has been changed to {$ticket->custom_fields['status']}",
            'creator_name' => TicketCreator::System->value,
        ]), [CommentTrelloTransport::class]);
    }

    /**
     * Complete a ticket.
     *
     * @param Ticket $ticket
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
