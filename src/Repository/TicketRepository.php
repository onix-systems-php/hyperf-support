<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Repository;

use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;
use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationRequestDTO;
use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationResultDTO;
use OnixSystemsPHP\HyperfCore\Model\Builder;
use OnixSystemsPHP\HyperfCore\Repository\AbstractRepository;
use OnixSystemsPHP\HyperfSupport\Model\Filter\TicketFilter;
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

    /**
     * @param array $filters
     * @param PaginationRequestDTO $paginationRequestDTO
     * @param array $contain
     * @return PaginationResultDTO
     */
    public function getPaginated(
        array $filters,
        PaginationRequestDTO $paginationRequestDTO,
        array $contain = []
    ): PaginationResultDTO {
        $query = $this->query()->filter(new TicketFilter($filters));
        if (!empty($contain)) {
            $query->with($contain);
        }

        return $query->paginateDTO($paginationRequestDTO);
    }

    /**
     * @param int $id
     * @return Collection|Model|array|Builder|Ticket|null
     */
    public function findById(int $id): Collection|Model|array|Builder|null|Ticket
    {
        return $this->query()->findOrFail($id);
    }

    /**
     * @param int $id
     * @param bool $lock
     * @param bool $force
     * @return Ticket|null
     */
    public function getById(int $id, bool $lock = false, bool $force = false): ?Ticket
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
     * @param string $slackId
     * @param bool $lock
     * @param bool $force
     * @return Ticket|null
     */
    public function getBySlackId(string $slackId, bool $lock = false, bool $force = false): ?Ticket
    {
        return $this->finder('slackId', $slackId)->fetchOne($lock, $force);
    }

    /**
     * @param Builder $query
     * @param string $slackId
     * @return void
     */
    public function scopeSlackId(Builder $query, string $slackId): void
    {
        $query->where('slack_id', $slackId);
    }

    /**
     * @param string $trelloId
     * @param bool $lock
     * @param bool $force
     * @return Ticket|null
     */
    public function getByTrelloId(string $trelloId, bool $lock = false, bool $force = false): ?Ticket
    {
        return $this->finder('trelloId', $trelloId)->fetchOne($lock, $force);
    }

    /**
     * @param Builder $query
     * @param string $trelloId
     * @return void
     */
    public function scopeTrelloId(Builder $query, string $trelloId): void
    {
        $query->where('trello_id', $trelloId);
    }
}
