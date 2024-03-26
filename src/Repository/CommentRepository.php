<?php

namespace OnixSystemsPHP\HyperfSupport\Repository;

use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;
use OnixSystemsPHP\HyperfCore\Model\Builder;
use OnixSystemsPHP\HyperfCore\Repository\AbstractRepository;
use OnixSystemsPHP\HyperfSupport\Model\Comment;

/**
 * @method Comment create(array $data)
 * @method Comment update(Comment $model, array $data)
 * @method Comment save(Comment $model)
 * @method bool delete(Comment $model)
 * @method Builder|CommentRepository finder(string $type, ...$parameters)
 * @method null|Comment fetchOne(bool $lock, bool $force)
 */

class CommentRepository extends AbstractRepository
{
    protected string $modelClass = Comment::class;

    public function getPaginated(int $perPage = 15): LengthAwarePaginatorInterface
    {
        return $this->query()->paginate($perPage);
    }

    public function findById(int $id): Collection|Model|Builder|array|Comment
    {
        return $this->query()->findOrFail($id);
    }

    public function getById(int $id, bool $lock = false, bool $force = false): ?Comment
    {
        return $this->finder('id', $id)->fetchOne($lock, $force);
    }

    public function scopeId(Builder $query, int $id): void
    {
        $query->where('id', $id);
    }

    public function getBySlackCommentId(string $slackCommentId, bool $lock = false, bool $force = false): ?Comment
    {
        return $this->finder('slackCommentId', $slackCommentId)->fetchOne($lock, $force);
    }

    public function scopeSlackCommentId(Builder $query, string $slackCommentId): void
    {
        $query->where('slack_comment_id', $slackCommentId);
    }

    public function getByTrelloId(string $trelloCommentId, bool $lock = false, bool $force = false): ?Comment
    {
        return $this->finder('trelloId', $trelloCommentId)->fetchOne($lock, $force);
    }

    public function scopeTrelloId(Builder $query, string $trelloCommentId): void
    {
        $query->where('trello_comment_id', $trelloCommentId);
    }
}
