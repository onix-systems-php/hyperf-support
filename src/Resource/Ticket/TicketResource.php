<?php

namespace OnixSystemsPHP\HyperfSupport\Resource\Ticket;

use OnixSystemsPHP\HyperfCore\Resource\AbstractResource;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Resource\User\UserResource;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'TicketResource', properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'title', type: 'string'),
    new OA\Property(property: 'content', type: 'string'),
    new OA\Property(property: 'source', type: 'string'),
    new OA\Property(property: 'custom_fields', type: 'object'),
    new OA\Property(property: 'creator', schema: '#/components/schemas/UserResource'),
    new OA\Property(property: 'modifier', schema: '#/components/schemas/UserResource'),
    new OA\Property(property: 'archiver', schema: '#/components/schemas/UserResource'),
    new OA\Property(property: 'completed_at', type: 'string'),
    new OA\Property(property: 'trello_url', type: 'string'),
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
            'creator' => UserResource::make($this->resource->creator),
            'editor' => UserResource::make($this->resource->editor),
            'archiver' => UserResource::make($this->resource->archiver),
            'completed_at' => $this->resource->completed_at,
            'trello_url' => $this->resource->trello_url,
            'page_url' => $this->resource->page_url,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->modified_at,
            'deleted_at' => $this->resource->deleted_at,
        ];
    }
}
