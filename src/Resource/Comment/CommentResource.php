<?php

namespace OnixSystemsPHP\HyperfSupport\Resource\Comment;

use OnixSystemsPHP\HyperfCore\Resource\AbstractResource;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'CommentResource', properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'ticket_id', type: 'integer'),
    new OA\Property(property: 'content', type: 'string'),
    new OA\Property(property: 'creator_name', type: 'string'),
    new OA\Property(property: 'trello_comment_id', type: 'string'),
    new OA\Property(property: 'slack_comment_id', type: 'string'),
    new OA\Property(property: 'created_by', type: 'integer'),
    new OA\Property(property: 'modified_by', type: 'integer'),
    new OA\Property(property: 'deleted_by', type: 'integer'),
    new OA\Property(property: 'created_at', type: 'string'),
    new OA\Property(property: 'modified_at', type: 'string'),
    new OA\Property(property: 'deleted_at', type: 'string'),
], type: 'object')]
/**
 * @method __construct(Comment $comment)
 * @property Comment $resource
 */
class CommentResource extends AbstractResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->resource->id,
            'ticket_id' => $this->resource->ticket_id,
            'content' => $this->resource->content,
            'creator_name' => $this->resource->creator_name,
            'trello_comment_id' => $this->resource->trello_comment_id,
            'slack_comment_id' => $this->resource->slack_comment_id,
            'created_by' => $this->resource->created_by,
            'modified_by' => $this->resource->modified_by,
            'deleted_by' => $this->resource->deleted_by,
            'created_at' => $this->resource->created_at,
            'modified_at' => $this->resource->modified_at,
            'deleted_at' => $this->resource->deleted_at,
        ];
    }
}
