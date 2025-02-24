<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Model\Filter;

use Hyperf\DbConnection\Db;
use OnixSystemsPHP\HyperfCore\Model\Filter\AbstractFilter;
use OpenApi\Attributes as OA;

#[OA\Parameter(parameter: 'TicketFilter__title', name: 'title', in: 'query', schema: new OA\Schema(
    type: 'string'
), example: 'Lorem ipsum')]
#[OA\Parameter(parameter: 'TicketFilter__source', name: 'source', in: 'query', schema: new OA\Schema(
    type: 'string'
), example: 'local')]
#[OA\Parameter(parameter: 'TicketFilter__user', name: 'user', in: 'query', schema: new OA\Schema(
    type: 'integer'
), example: 1)]
#[OA\Parameter(parameter: 'TicketFilter__team_id', name: 'team_id', in: 'query', schema: new OA\Schema(
    type: 'integer'
), example: 1)]
class TicketFilter extends AbstractFilter
{
    public function title(string $param): void
    {
        $this->builder->where('title', 'ilike', "%$param%");
    }

    public function source(string $param): void
    {
        $this->builder->where('source', $param);
    }

    public function user(int $param): void
    {
        $this->builder
            ->where('created_by', $param)
            ->orWhere('modified_by', $param)
            ->orWhere('deleted_by', $param);
    }

    public function teamId(int $param): void
    {
        $projectIds = DB::table('projects')
            ->where('team_id', $param)
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();
        $this->builder->whereIn('source', $projectIds);
    }
}
