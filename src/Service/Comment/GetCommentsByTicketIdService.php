<?php

declare(strict_types=1);

namespace OnixSystemsPHP\HyperfSupport\Service\Comment;

use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationRequestDTO;
use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationResultDTO;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;

class GetCommentsByTicketIdService
{
    public function __construct(private readonly CommentRepository $commentRepository)
    {
    }

    /**
     * @param PaginationRequestDTO $paginationRequestDTO
     * @param int $ticketId
     * @return PaginationResultDTO
     */
    public function run(PaginationRequestDTO $paginationRequestDTO, int $ticketId): PaginationResultDTO
    {
        return $this->commentRepository->getCommentsByTicketIdPaginated($paginationRequestDTO, $ticketId);
    }
}
