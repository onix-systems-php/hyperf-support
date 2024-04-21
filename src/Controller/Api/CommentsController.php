<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Controller\Api;

use Hyperf\HttpServer\Contract\RequestInterface;
use OnixSystemsPHP\HyperfCore\Controller\AbstractController;
use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationRequestDTO;
use OnixSystemsPHP\HyperfCore\Resource\ResourceSuccess;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\CreateCommentDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\UpdateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Request\Comments\CreateCommentRequest;
use OnixSystemsPHP\HyperfSupport\Request\Comments\UpdateCommentRequest;
use OnixSystemsPHP\HyperfSupport\Resource\Comment\CommentResource;
use OnixSystemsPHP\HyperfSupport\Resource\Comment\CommentsPaginatedResource;
use OnixSystemsPHP\HyperfSupport\Service\Comment\CreateCommentService;
use OnixSystemsPHP\HyperfSupport\Service\Comment\DeleteCommentService;
use OnixSystemsPHP\HyperfSupport\Service\Comment\GetCommentService;
use OnixSystemsPHP\HyperfSupport\Service\Comment\GetCommentsService;
use OnixSystemsPHP\HyperfSupport\Service\Comment\UpdateCommentService;
use OpenApi\Attributes as OA;

class CommentsController extends AbstractController
{
    public function __construct()
    {
    }

    #[OA\Get(
        path: '/v1/support/comments',
        operationId: 'getComments',
        summary: 'Get list of comments',
        tags: ['comments'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/Pagination_page'),
            new OA\Parameter(ref: '#/components/parameters/Pagination_per_page'),
            new OA\Parameter(ref: '#/components/parameters/Pagination_order'),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/CommentsPaginatedResource'),
            ])),
            new OA\Response(ref: '#/components/responses/401', response: 401),
            new OA\Response(ref: '#/components/responses/403', response: 403),
            new OA\Response(ref: '#/components/responses/500', response: 500),
        ],
    )]
    public function index(RequestInterface $request, GetCommentsService $getCommentsService): CommentsPaginatedResource
    {
        $paginationDTO = PaginationRequestDTO::make($request);
        $commentsPaginationResult = $getCommentsService->run($paginationDTO);

        return CommentsPaginatedResource::make($commentsPaginationResult);
    }

    #[OA\Post(
        path: '/v1/support/comments',
        operationId: 'addComment',
        summary: 'Create a comment',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateCommentRequest')
        ),
        tags: ['comments'],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/CommentResource'),
            ])),
            new OA\Response(ref: '#/components/responses/400', response: 400),
            new OA\Response(ref: '#/components/responses/401', response: 401),
            new OA\Response(ref: '#/components/responses/403', response: 403),
            new OA\Response(ref: '#/components/responses/422', response: 422),
            new OA\Response(ref: '#/components/responses/500', response: 500),
        ],
    )]
    public function store(CreateCommentRequest $request, CreateCommentService $createCommentService): CommentResource
    {
        $comment = $createCommentService->run(CreateCommentDTO::make($request));

        return CommentResource::make($comment);
    }

    #[OA\Get(
        path: '/v1/support/comments/{id}',
        operationId: 'getCommentById',
        summary: 'Get a comment by id',
        tags: ['comments'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Comment id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/CommentResource'),

            ])),
            new OA\Response(ref: '#/components/responses/401', response: 401),
            new OA\Response(ref: '#/components/responses/403', response: 403),
            new OA\Response(ref: '#/components/responses/500', response: 500),
        ],
    )]
    public function show(int $id, GetCommentService $getCommentService): ?CommentResource
    {
        $comment = $getCommentService->run($id);

        return isset($comment) ? CommentResource::make($comment) : null;
    }

    #[OA\Put(
        path: '/v1/support/comment/{id}',
        operationId: 'updateComment',
        summary: 'Update the comment',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateCommentRequest')
        ),
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Comment id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(ref: '#/components/schemas/CommentResource'),
            ])),
            new OA\Response(ref: '#/components/responses/400', response: 400),
            new OA\Response(ref: '#/components/responses/401', response: 401),
            new OA\Response(ref: '#/components/responses/403', response: 403),
            new OA\Response(ref: '#/components/responses/422', response: 404),
            new OA\Response(ref: '#/components/responses/500', response: 500),
        ],
    )]
    public function update(
        int $id,
        UpdateCommentRequest $request,
        UpdateCommentService $updateCommentService
    ): ?CommentResource {
        $commentUpdated = $updateCommentService->run($id, UpdateCommentDTO::make($request));

        return isset($commentUpdated) ? CommentResource::make($commentUpdated) : null;
    }

    #[OA\Delete(
        path: '/v1/support/tickets/{id}',
        operationId: 'deleteComment',
        summary: 'Delete the comment',
        tags: ['tickets'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Comment id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ResourceSuccess'),
            ])),
            new OA\Response(ref: '#/components/responses/400', response: 400),
            new OA\Response(ref: '#/components/responses/401', response: 401),
            new OA\Response(ref: '#/components/responses/403', response: 403),
            new OA\Response(ref: '#/components/responses/422', response: 404),
            new OA\Response(ref: '#/components/responses/500', response: 500),
        ],
    )]
    public function destroy(int $id, DeleteCommentService $deleteCommentService): ResourceSuccess
    {
        $deleteCommentService->run($id);

        return new ResourceSuccess([]);
    }
}
