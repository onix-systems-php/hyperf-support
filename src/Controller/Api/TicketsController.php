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
use OnixSystemsPHP\HyperfSupport\DTO\Tickets\CreateTicketDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Tickets\DeleteTicketDTO;
use OnixSystemsPHP\HyperfSupport\DTO\Tickets\UpdateTicketDTO;
use OnixSystemsPHP\HyperfSupport\Request\Tickets\CreateTicketRequest;
use OnixSystemsPHP\HyperfSupport\Request\Tickets\UpdateTicketRequest;
use OnixSystemsPHP\HyperfSupport\Resource\Ticket\TicketResource;
use OnixSystemsPHP\HyperfSupport\Resource\Ticket\TicketsPaginatedResource;
use OnixSystemsPHP\HyperfSupport\Service\Ticket\CreateTicketService;
use OnixSystemsPHP\HyperfSupport\Service\Ticket\DeleteTicketService;
use OnixSystemsPHP\HyperfSupport\Service\Ticket\GetTicketService;
use OnixSystemsPHP\HyperfSupport\Service\Ticket\GetTicketsService;
use OnixSystemsPHP\HyperfSupport\Service\Ticket\UpdateTicketService;
use OpenApi\Attributes as OA;

class TicketsController extends AbstractController
{
    public function __construct()
    {
    }

    #[OA\Get(
        path: '/v1/support/tickets',
        operationId: 'getTickets',
        summary: 'Get list of tickets',
        tags: ['tickets'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/Pagination_page'),
            new OA\Parameter(ref: '#/components/parameters/Pagination_per_page'),
            new OA\Parameter(ref: '#/components/parameters/Pagination_order'),
            new OA\Parameter(ref: '#/components/parameters/TicketFilter__title'),
            new OA\Parameter(ref: '#/components/parameters/TicketFilter__source'),
            new OA\Parameter(ref: '#/components/parameters/TicketFilter__user'),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/TicketsPaginatedResource'),
            ])),
            new OA\Response(ref: '#/components/responses/401', response: 401),
            new OA\Response(ref: '#/components/responses/403', response: 403),
            new OA\Response(ref: '#/components/responses/500', response: 500),
        ],
    )]
    public function index(RequestInterface $request, GetTicketsService $getTicketsService): TicketsPaginatedResource
    {
        $paginationDTO = PaginationRequestDTO::make($request);
        $ticketsPaginationResult = $getTicketsService->run(
            $this->request->getQueryParams(),
            $paginationDTO,
        );

        return TicketsPaginatedResource::make($ticketsPaginationResult);
    }

    #[OA\Post(
        path: '/v1/support/tickets',
        operationId: 'addTicket',
        summary: 'Create a ticket',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateTicketRequest')
        ),
        tags: ['tickets'],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/TicketResource'),
            ])),
            new OA\Response(ref: '#/components/responses/400', response: 400),
            new OA\Response(ref: '#/components/responses/401', response: 401),
            new OA\Response(ref: '#/components/responses/403', response: 403),
            new OA\Response(ref: '#/components/responses/422', response: 422),
            new OA\Response(ref: '#/components/responses/500', response: 500),
        ],
    )]
    public function store(CreateTicketRequest $request, CreateTicketService $createTicketService): TicketResource
    {
        $ticket = $createTicketService->run(CreateTicketDTO::make($request));

        return TicketResource::make($ticket);
    }

    #[OA\Get(
        path: '/v1/support/tickets/{id}',
        operationId: 'getTicketById',
        summary: 'Get a ticket by id',
        tags: ['tickets'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Ticket id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/TicketResource'),

            ])),
            new OA\Response(ref: '#/components/responses/401', response: 401),
            new OA\Response(ref: '#/components/responses/403', response: 403),
            new OA\Response(ref: '#/components/responses/500', response: 500),
        ],
    )]
    public function show(int $id, GetTicketService $getTicketService): TicketResource
    {
        return TicketResource::make($getTicketService->run($id));
    }

    #[OA\Put(
        path: '/v1/support/tickets/{id}',
        operationId: 'updateTicket',
        summary: 'Update the ticket',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateTicketRequest')
        ),
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Ticket id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(ref: '#/components/schemas/TicketResource'),
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
        UpdateTicketRequest $request,
        UpdateTicketService $updateTicketService
    ): TicketResource {
        return TicketResource::make($updateTicketService->run($id, UpdateTicketDTO::make($request)));
    }

    #[OA\Delete(
        path: '/v1/support/tickets/{id}',
        operationId: 'deleteTicket',
        summary: 'Delete the ticket',
        tags: ['tickets'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Ticket id',
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
    public function destroy(int $id, DeleteTicketService $deleteTicketService): ResourceSuccess
    {
        $deleteTicketService->run($id);

        return new ResourceSuccess([]);
    }
}
