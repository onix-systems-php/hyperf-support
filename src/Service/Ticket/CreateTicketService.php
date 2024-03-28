<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Service\Ticket;

use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\Rule;
use OnixSystemsPHP\HyperfActionsLog\Event\Action;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfSupport\Adapter\SupportAdapter;
use OnixSystemsPHP\HyperfSupport\Constant\Actions;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\DTO\Tickets\CreateTicketDTO;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Repository\TicketRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateTicketRequest',
    properties: [
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'content', type: 'string'),
        new OA\Property(property: 'source', type: 'string'),
        new OA\Property(property: 'custom_fields', type: 'object', example: '{"type": "Feature Request"}'),
        new OA\Property(property: 'page_url', type: 'string'),
        new OA\Property(property: 'created_by', type: 'integer'),
    ],
    type: 'object',
)]
readonly class CreateTicketService
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private ValidatorFactoryInterface $validatorFactory,
        private EventDispatcherInterface $eventDispatcher,
        private ?CorePolicyGuard $policyGuard,
        private SupportAdapter $supportAdapter,
        private SourceConfiguratorInterface $sourceConfigurator,
    ) {}

    /**
     * Create a ticket.
     *
     * @param CreateTicketDTO $createTicketDTO
     * @return Ticket
     */
    public function run(CreateTicketDTO $createTicketDTO): Ticket
    {
        $this->validate($createTicketDTO);

        $this->policyGuard?->check('create', new Ticket());
        $ticket = $this->ticketRepository->create($createTicketDTO->toArray());
        $this->ticketRepository->save($ticket);

        $this->eventDispatcher->dispatch(new Action(Actions::CREATE_TICKET, $ticket, $createTicketDTO->toArray()));
        $this->supportAdapter->run(Actions::CREATE_TICKET, $ticket);

        return $ticket;
    }

    /**
     * @param CreateTicketDTO $createTicketDTO
     * @return void
     */
    public function validate(CreateTicketDTO $createTicketDTO): void
    {
        $this->validatorFactory->make($createTicketDTO->toArray(), ['source' => 'required'])->validate();

        $validationRules = [
            'title' => ['required', 'min:5', 'max:255'],
            'content' => ['required', 'min:20'],
            'custom_fields' => ['array'],
            'page_url' => ['url:https'],
            'created_by' => ['required', 'integer'],
        ];

        $configValues = $this->sourceConfigurator->getApiConfig($createTicketDTO->source, 'ticket', 'custom_fields');
        foreach ($configValues as $key => $value) {
            $validationRules['custom_fields.' . $key] = [
                'required',
                'max:255',
                Rule::in($value)
            ];
        }
        $this->validatorFactory->make($createTicketDTO->toArray(), $validationRules)->validate();
    }
}
