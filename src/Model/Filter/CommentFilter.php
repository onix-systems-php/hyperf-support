<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Model\Filter;

use OnixSystemsPHP\HyperfCore\Model\Filter\AbstractFilter;
use OpenApi\Attributes as OA;

#[OA\Parameter(
    parameter: 'CommentFilter__ticket_id',
    name: 'ticket_id',
    in: 'query',
    schema: new OA\Schema(type: 'integer'),
    example: 1
)]
#[OA\Parameter(
    parameter: 'CommentFilter__creator_name',
    name: 'creator_name',
    in: 'query',
    schema: new OA\Schema(type: 'string'),
    example: 'John Dou'
)]
#[OA\Parameter(
    parameter: 'CommentFilter__trello_comment_id',
    name: 'trello_comment_id',
    in: 'query',
    schema: new OA\Schema(type: 'string'),
    example: '668td7f362defbbdvcfd780c'
)]
#[OA\Parameter(
    parameter: 'CommentFilter__slack_comment_id',
    name: 'slack_comment_id',
    in: 'query',
    schema: new OA\Schema(type: 'string'),
    example: '1721165713.104594'
)]
#[OA\Parameter(
    parameter: 'CommentFilter__created_by',
    name: 'created_by',
    in: 'query',
    schema: new OA\Schema(type: 'integer'),
    example: 1
)]
#[OA\Parameter(
    parameter: 'CommentFilter__modified_by',
    name: 'modified_by',
    in: 'query',
    schema: new OA\Schema(type: 'integer'),
    example: 1
)]
#[OA\Parameter(
    parameter: 'CommentFilter__deleted_by',
    name: 'deleted_by',
    in: 'query',
    schema: new OA\Schema(type: 'integer'),
    example: 1
)]
class CommentFilter extends AbstractFilter
{
    public function ticketId(int $param): void
    {
        $this->builder->where('ticket_id', '=', $param);
    }

    public function creatorName(string $param): void
    {
        $this->builder->where('creator_name', 'ilike', "%$param%");
    }

    public function trelloCommentId(string $param): void
    {
        $this->builder->where('trello_comment_id', '=', $param);
    }

    public function slackCommentId(string $param): void
    {
        $this->builder->where('slack_comment_id', '=', $param);
    }

    public function createdBy(int $param): void
    {
        $this->builder->where('created_by', '=', $param);
    }

    public function modifiedBy(int $param): void
    {
        $this->builder->where('modified_by', '=', $param);
    }

    public function deletedBy(int $param): void
    {
        $this->builder->where('deleted_by', '=', $param);
    }
}
