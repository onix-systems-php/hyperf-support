<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Comment;

use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationRequestDTO;
use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationResultDTO;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;

class GetCommentsService
{
    public function __construct(private readonly CommentRepository $commentRepository)
    {
    }

    /**
     * Get paginated comments.
     *
     * @param PaginationRequestDTO $paginationRequestDTO
     * @return PaginationResultDTO
     */
    public function run(PaginationRequestDTO $paginationRequestDTO): PaginationResultDTO
    {
        return $this->commentRepository->getPaginated($paginationRequestDTO);
    }
}
