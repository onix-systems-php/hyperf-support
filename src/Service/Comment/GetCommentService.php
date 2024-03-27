<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Comment;

use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Repository\CommentRepository;

readonly class GetCommentService
{
    public function __construct(private CommentRepository $commentRepository) {}

    /**
     * Get the comment by id.
     *
     * @param int $id
     * @return Comment|null
     */
    public function run(int $id): ?Comment
    {
        return $this->commentRepository->findById($id);
    }
}
