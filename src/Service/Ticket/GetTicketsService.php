<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Ticket;

use Hyperf\Contract\LengthAwarePaginatorInterface;
use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationRequestDTO;
use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationResultDTO;
use OnixSystemsPHP\HyperfSupport\Repository\TicketRepository;

readonly class GetTicketsService
{
    public function __construct(private TicketRepository $ticketRepository) {}

    /**
     * Get paginated tickets.
     *
     * @param array $filters
     * @param PaginationRequestDTO $paginationRequestDTO
     * @param array $contain
     * @return PaginationResultDTO
     */
    public function run(
        array $filters,
        PaginationRequestDTO $paginationRequestDTO,
        array $contain = []
    ): PaginationResultDTO
    {
        return $this->ticketRepository->getPaginated($filters, $paginationRequestDTO, $contain);
    }
}
