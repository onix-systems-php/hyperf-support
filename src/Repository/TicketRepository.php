<?php

namespace OnixSystemsPHP\HyperfSupport\Repository;

use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;
use OnixSystemsPHP\HyperfCore\Model\Builder;
use OnixSystemsPHP\HyperfCore\Repository\AbstractRepository;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

/**
 * @method Ticket create(array $data)
 * @method Ticket update(Ticket $model, array $data)
 * @method Ticket save(Ticket $model)
 * @method bool delete(Ticket $model)
 * @method Builder|TicketRepository finder(string $type, ...$parameters)
 * @method Ticket|null fetchOne(bool $lock, bool $force)
 */
class TicketRepository extends AbstractRepository
{
    protected string $modelClass = Ticket::class;

    public function getPaginated(int $perPage): LengthAwarePaginatorInterface
    {
        return $this->query()->paginate($perPage);
    }

    public function findById(int $id): Collection|Model|array|Builder|null|Ticket
    {
        return $this->query()->findOrFail($id);
    }

    public function getById(int $id, bool $lock = false, bool $force = false): ?Ticket
    {
        return $this->finder('id', $id)->fetchOne($lock, $force);
    }

    public function scopeId(Builder $query, int $id): void
    {
        $query->where('id', $id);
    }

    public function getBySlackId(string $slackId, bool $lock = false, bool $force = false): ?Ticket
    {
        return $this->finder('slackId', $slackId)->fetchOne($lock, $force);
    }

    public function scopeSlackId(Builder $query, string $slackId): void
    {
        $query->where('slack_id', $slackId);
    }

    public function getByTrelloId(string $trelloId, bool $lock = false, bool $force = false): ?Ticket
    {
        return $this->finder('trelloId', $trelloId)->fetchOne($lock, $force);
    }

    public function scopeTrelloId(Builder $query, string $trelloId): void
    {
        $query->where('trello_id', $trelloId);
    }
}
