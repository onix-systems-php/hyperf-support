<?php

namespace OnixSystemsPHP\HyperfSupport\Model\Filter;

use OnixSystemsPHP\HyperfCore\Model\Filter\AbstractFilter;
use OpenApi\Attributes as OA;

#[OA\Parameter(parameter: 'CommentFilter__creator_name', name: 'creator_name', in: 'query', schema: new OA\Schema(
    type: 'string'
), example: 'local')]
class CommentFilter extends AbstractFilter
{
    public function creatorName(string $param): void
    {
        $this->builder->where('creator_name', 'like', "%$param%");
    }
}
