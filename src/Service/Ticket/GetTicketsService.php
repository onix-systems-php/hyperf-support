<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Ticket;

use Hyperf\Contract\LengthAwarePaginatorInterface;
use OnixSystemsPHP\HyperfSupport\Repository\TicketRepository;

readonly class GetTicketsService
{
    public function __construct(private TicketRepository $ticketRepository) {}

    /**
     * Get paginated tickets.
     *
     * @return LengthAwarePaginatorInterface
     */
    public function run(): LengthAwarePaginatorInterface
    {
        return $this->ticketRepository->getPaginated();
    }
}
