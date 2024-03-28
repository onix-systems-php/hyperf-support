<?php

namespace OnixSystemsPHP\HyperfSupport\Resource\Ticket;

use OnixSystemsPHP\HyperfCore\Resource\AbstractPaginatedResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TicketsPaginatedResource',
    properties: [
        new OA\Property(property: 'list', type: 'array', items: new OA\Items(ref: '#/components/schemas/TicketResource')),
        new OA\Property(property: 'total', type: 'integer'),
        new OA\Property(property: 'page', type: 'integer'),
        new OA\Property(property: 'per_page', type: 'integer'),
        new OA\Property(property: 'total_pages', type: 'integer'),
    ],
    type: 'object',
)]
class TicketsPaginatedResource extends AbstractPaginatedResource
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), ['list' => TicketResource::collection($this->resource->list)]);
    }
}
