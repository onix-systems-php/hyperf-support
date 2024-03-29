<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Adapter;

use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Contract\TransportInterface;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use RuntimeException;

use function Hyperf\Support\make;

readonly class SupportAdapter
{
    public function __construct(private SourceConfiguratorInterface $sourceConfigurator) {}

    /**
     * Create the transport for given event and entity and run it.
     *
     * @param string $event
     * @param Ticket|Comment $entity
     * @param array $shouldBeSkipped
     * @return void
     */
    public function run(string $event, Ticket|Comment $entity, array $shouldBeSkipped = []): void
    {
        [$action, $type] = explode('-', $event);
        $transports = match(true) {
            $entity instanceof Ticket => $this->sourceConfigurator->getApiConfig($entity->source, 'transports', $type),
            $entity instanceof Comment => $this->sourceConfigurator->getApiConfig($entity->ticket->source, 'transports', $type),
            default => [],
        };
        foreach ($transports as $transport) {
            if (in_array($transport, $shouldBeSkipped)) {
                continue;
            }
            $instance = make($transport);
            if (!$instance instanceof TransportInterface) {
                throw new RuntimeException("$transport does not implement the TransportInterface.");
            }
            $instance->run($action, $entity);
        }
    }
}
