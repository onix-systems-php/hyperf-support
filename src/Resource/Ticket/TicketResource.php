<?php

namespace OnixSystemsPHP\HyperfSupport\Resource\Ticket;

use OnixSystemsPHP\HyperfCore\Resource\AbstractResource;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'TicketResource', properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'title', type: 'string'),
    new OA\Property(property: 'content', type: 'string'),
    new OA\Property(property: 'source', type: 'string'),
    new OA\Property(property: 'custom_fields', type: 'object'),
    new OA\Property(property: 'created_by', type: 'integer'),
    new OA\Property(property: 'modified_by', type: 'integer'),
    new OA\Property(property: 'deleted_by', type: 'integer'),
    new OA\Property(property: 'completed_at', type: 'string'),
    new OA\Property(property: 'trello_id', type: 'string'),
    new OA\Property(property: 'slack_id', type: 'string'),
    new OA\Property(property: 'trello_short_link', type: 'string'),
    new OA\Property(property: 'page_url', type: 'string'),
    new OA\Property(property: 'created_at', type: 'string'),
    new OA\Property(property: 'updated_at', type: 'string'),
    new OA\Property(property: 'deleted_at', type: 'string'),
], type: 'object')]
/**
 * @method __construct(Ticket $resource)
 * @property Ticket $resource
 */
class TicketResource extends AbstractResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'content' => $this->resource->content,
            'source' => $this->resource->source,
            'custom_fields' => $this->resource->custom_fields,
            'created_by' => $this->resource->created_by,
            'modified_by' => $this->resource->modified_by,
            'deleted_by' => $this->resource->deleted_by,
            'completed_at' => $this->resource->completed_at,
            'trello_id' => $this->resource->trello_id,
            'slack_id' => $this->resource->slack_id,
            'trello_short_link' => $this->resource->trello_short_link,
            'page_url' => $this->resource->page_url,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->modified_at,
            'deleted_at' => $this->resource->deleted_at,
        ];
    }
}
