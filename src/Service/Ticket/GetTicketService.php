<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Ticket;

use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Repository\TicketRepository;
use function Hyperf\Support\now;

class GetTicketService
{
    public function __construct(private readonly TicketRepository $ticketRepository)
    {
    }

    /**
     * Get the ticket by id.
     *
     * @param int $id
     * @return Ticket
     */
    public function run(int $id): Ticket
    {
        $ticket = $this->ticketRepository->getById($id, false, true);
        if($ticket !== null && $ticket->seen_at === null) {
            $ticket->seen_at = now();
            $ticket->save();
        }
        return $ticket;
    }
}
