<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Ticket;

use Exception;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use OnixSystemsPHP\HyperfActionsLog\Event\Action;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfSupport\Adapter\SupportAdapter;
use OnixSystemsPHP\HyperfSupport\Constant\Actions;
use OnixSystemsPHP\HyperfSupport\DTO\Tickets\DeleteTicketDTO;
use OnixSystemsPHP\HyperfSupport\Repository\TicketRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class DeleteTicketService
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private ?CorePolicyGuard $policyGuard,
        private EventDispatcherInterface $eventDispatcher,
        private SupportAdapter $supportAdapter,
        private ValidatorFactoryInterface $validatorFactory
    ) {}

    /**
     * Delete the given ticket.
     *
     * @throws Exception
     */
    public function run(int $id, DeleteTicketDTO $deleteTicketDTO): bool
    {
        $ticket = $this->ticketRepository->findById($id);
        $this->validate($deleteTicketDTO);

        $this->policyGuard?->check('delete', $ticket);
        $this->ticketRepository->update($ticket, $deleteTicketDTO->toArray());
        $this->ticketRepository->save($ticket);

        $result = $this->ticketRepository->delete($ticket);

        $this->eventDispatcher->dispatch(new Action(Actions::DELETE_TICKET, $ticket, $ticket->toArray()));
        $this->supportAdapter->run(Actions::DELETE_TICKET, $ticket);

        return $result;
    }

    /**
     * @param DeleteTicketDTO $deleteTicketDTO
     * @return void
     */
    public function validate(DeleteTicketDTO $deleteTicketDTO): void
    {
        $this->validatorFactory->make($deleteTicketDTO->toArray(), [
            'deleted_by' => ['required'],
        ])->validate();
    }
}
