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

class GetCommentService
{
    public function __construct(private readonly CommentRepository $commentRepository) {}

    /**
     * Get the comment by id.
     *
     * @param int $id
     * @return Comment
     */
    public function run(int $id): Comment
    {
        return $this->commentRepository->getById($id, false, true);
    }
}
