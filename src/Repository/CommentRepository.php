<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Repository;

use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationRequestDTO;
use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationResultDTO;
use OnixSystemsPHP\HyperfCore\Model\Builder;
use OnixSystemsPHP\HyperfCore\Repository\AbstractRepository;
use OnixSystemsPHP\HyperfSupport\Model\Comment;

/**
 * @method Comment create(array $data)
 * @method Comment update(Comment $model, array $data)
 * @method bool save(Comment $model)
 * @method bool delete(Comment $model)
 * @method Builder|CommentRepository finder(string $type, ...$parameters)
 * @method null|Comment fetchOne(bool $lock, bool $force)
 */
class CommentRepository extends AbstractRepository
{
    protected string $modelClass = Comment::class;

    /**
     * @param PaginationRequestDTO $paginationRequestDTO
     * @param array $contain
     * @return PaginationResultDTO
     */
    public function getPaginated(PaginationRequestDTO $paginationRequestDTO, array $contain = []): PaginationResultDTO
    {
        $query = $this->query();
        if (!empty($contain)) {
            $query->with($contain);
        }

        return $query->paginateDTO($paginationRequestDTO);
    }

    /**
     * @param int $id
     * @param bool $lock
     * @param bool $force
     * @return Comment|null
     */
    public function getById(int $id, bool $lock = false, bool $force = false): ?Comment
    {
        return $this->finder('id', $id)->fetchOne($lock, $force);
    }

    /**
     * @param Builder $query
     * @param int $id
     * @return void
     */
    public function scopeId(Builder $query, int $id): void
    {
        $query->where('id', $id);
    }

    /**
     * @param string $slackCommentId
     * @param bool $lock
     * @param bool $force
     * @return Comment|null
     */
    public function getBySlackCommentId(string $slackCommentId, bool $lock = false, bool $force = false): ?Comment
    {
        return $this->finder('slackCommentId', $slackCommentId)->fetchOne($lock, $force);
    }

    /**
     * @param Builder $query
     * @param string $slackCommentId
     * @return void
     */
    public function scopeSlackCommentId(Builder $query, string $slackCommentId): void
    {
        $query->where('slack_comment_id', $slackCommentId);
    }

    /**
     * @param string $trelloCommentId
     * @param bool $lock
     * @param bool $force
     * @return Comment|null
     */
    public function getByTrelloId(string $trelloCommentId, bool $lock = false, bool $force = false): ?Comment
    {
        return $this->finder('trelloId', $trelloCommentId)->fetchOne($lock, $force);
    }

    /**
     * @param Builder $query
     * @param string $trelloCommentId
     * @return void
     */
    public function scopeTrelloId(Builder $query, string $trelloCommentId): void
    {
        $query->where('trello_comment_id', $trelloCommentId);
    }

    /**
     * @param PaginationRequestDTO $paginationRequestDTO
     * @param int $ticketId
     * @return PaginationResultDTO
     */
    public function getCommentsByTicketIdPaginated(PaginationRequestDTO $paginationRequestDTO, int $ticketId): PaginationResultDTO
    {
        return $this->finder('ticketId', $ticketId)->orderByDesc('created_at')->paginateDTO($paginationRequestDTO);
    }

    /**
     * @param Builder $query
     * @param int $ticketId
     * @return void
     */
    public function scopeTicketId(Builder $query, int $ticketId): void
    {
        $query->where('ticket_id', $ticketId);
    }
}
