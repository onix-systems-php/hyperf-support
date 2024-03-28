<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Controller\Api;

use Hyperf\HttpServer\Annotation\Controller;
use OnixSystemsPHP\HyperfCore\Controller\AbstractController;
use OnixSystemsPHP\HyperfCore\DTO\Common\PaginationRequestDTO;
use OnixSystemsPHP\HyperfCore\Resource\ResourceSuccess;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\CreateCommentDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Comments\UpdateCommentDTO;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Resource\Comment\CommentResource;
use OnixSystemsPHP\HyperfSupport\Resource\Comment\CommentsPaginatedResource;
use OnixSystemsPHP\HyperfSupport\Service\Comment\CreateCommentService;
use OnixSystemsPHP\HyperfSupport\Service\Comment\DeleteCommentService;
use OnixSystemsPHP\HyperfSupport\Service\Comment\GetCommentService;
use OnixSystemsPHP\HyperfSupport\Service\Comment\GetCommentsService;
use OnixSystemsPHP\HyperfSupport\Service\Comment\UpdateCommentService;
use Psr\Http\Message\ResponseInterface;
use OpenApi\Attributes as OA;

#[Controller]
class CommentsController extends AbstractController
{
    public function __construct(
        private readonly GetCommentsService $getCommentsService,
        private readonly GetCommentService $getCommentService,
        private readonly CreateCommentService $createCommentService,
        private readonly UpdateCommentService $updateCommentService,
        private readonly DeleteCommentService $deleteCommentService,
    ) {}

    #[OA\Get(
        path: '/v1/support/comments',
        operationId: 'getComments',
        summary: 'Get list of comments',
        tags: ['comments'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/Pagination_page'),
            new OA\Parameter(ref: '#/components/parameters/Pagination_per_page'),
            new OA\Parameter(ref: '#/components/parameters/Pagination_order'),
            new OA\Parameter(ref: '#/components/parameters/CommentFilter__creator_name'),
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
    public function index(): CommentsPaginatedResource
    {
        $paginationDTO = PaginationRequestDTO::make($this->request);
        $commentsPaginationResult = $this->getCommentsService->run($this->request->getQueryParams(), $paginationDTO);

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
    public function store(): Comment
    {
        return $this->createCommentService->run(CreateCommentDTO::make($this->request->all()));
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
    public function show(int $id): CommentResource
    {
        return CommentResource::make($this->getCommentService->run($id));
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
    public function update(int $id): ?Comment
    {
        return $this->updateCommentService->run($id, UpdateCommentDTO::make($this->request->all()));
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
    public function destroy(int $id): ResourceSuccess
    {
        $this->deleteCommentService->run($id);

        return new ResourceSuccess([]);
    }
}
