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
use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfSupport\Adapter\SupportAdapter;
use OnixSystemsPHP\HyperfSupport\Constant\Actions;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\DTO\Tickets\CreateTicketDTO;
use OnixSystemsPHP\HyperfSupport\Events\TicketCreated;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Repository\TicketRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

class CreateTicketService
{
    public function __construct(
        private readonly TicketRepository $ticketRepository,
        private readonly ValidatorFactoryInterface $validatorFactory,
        private readonly SupportAdapter $supportAdapter,
        private readonly CoreAuthenticatableProvider $coreAuthenticatableProvider,
        private readonly SourceConfiguratorInterface $sourceConfigurator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ?CorePolicyGuard $policyGuard,
    ) {
    }

    /**
     * Create a ticket.
     *
     * @param CreateTicketDTO $createTicketDTO
     * @return Ticket
     */
    public function run(CreateTicketDTO $createTicketDTO): Ticket
    {
        $this->validate($createTicketDTO);

        $ticketData = array_merge(
            $createTicketDTO->toArray(),
            [
                'created_by' => $this->coreAuthenticatableProvider->user()?->getId(),
            ]
        );

        $ticket = $this->ticketRepository->create($ticketData);
        $this->policyGuard?->check('create', $ticket);
        $this->ticketRepository->save($ticket);

        $this->eventDispatcher->dispatch(new TicketCreated($ticket));
        $this->eventDispatcher->dispatch(new Action(Actions::CREATE_TICKET, $ticket, $ticketData));

        $this->supportAdapter->run(Actions::CREATE_TICKET, $ticket);

        return $ticket;
    }

    /**
     * @param CreateTicketDTO $createTicketDTO
     * @return void
     */
    private function validate(CreateTicketDTO $createTicketDTO): void
    {
        $this->validatorFactory->make(
            $createTicketDTO->toArray(),
            [
                'source' => ['required'],
                'custom_fields' => ['present', 'array'],
                'custom_fields.type' => ['required', 'string'],
            ]
        )->validate();

        $validationRules = [
            'title' => ['required', 'min:5', 'max:255'],
            'content' => ['required', 'min:20'],
            'page_url' => ['url:https'],
        ];

        $ticketType = (string)$createTicketDTO->custom_fields['type'];

        /** @var string[]|null $requiredFields */
        $requiredFields = $this->sourceConfigurator->getApiConfig(
            $createTicketDTO->source,
            'ticket',
            'required_fields',
        )[$ticketType];

        /** @var array<string, string[]> $acceptedValues */
        $acceptedValues = $this->sourceConfigurator->getApiConfig(
            $createTicketDTO->source,
            'ticket',
            'custom_fields',
        );

        foreach ($requiredFields ?? [] as $requiredField) {
            $acceptedFieldValues = $acceptedValues[$requiredField];

            $validationRules['custom_fields.' . $requiredField] = [
                'required',
                'max:255',
                Rule::in($acceptedFieldValues)
            ];
        }

        $this->validatorFactory->make($createTicketDTO->toArray(), $validationRules)->validate();
    }
}
