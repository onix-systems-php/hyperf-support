<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Comment;

use Hyperf\Contract\LengthAwarePaginatorInterface;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;

readonly class GetCommentsService
{
    public function __construct(private CommentRepository $commentRepository) {}

    /**
     * Get paginated comments.
     *
     * @return LengthAwarePaginatorInterface
     */
    public function run(): LengthAwarePaginatorInterface
    {
        return $this->commentRepository->getPaginated();
    }
}
