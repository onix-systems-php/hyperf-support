<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Adapter;

use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Model\Comment;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;
use OnixSystemsPHP\HyperfSupport\Transport\TransportInterface;
use RuntimeException;

use function Hyperf\Support\make;

readonly class SupportAdapter
{
    public function __construct(private SourceConfiguratorInterface $sourceConfigurator) {}

    /**
     * Run the Ticket or Comment transport.
     *
     * @param string $event
     * @param Ticket|Comment $entity
     * @param string $shouldBeSkipped
     * @return void
     */
    public function run(string $event, Ticket|Comment $entity, array $shouldBeSkipped = []): void
    {
        [$action, $type] = explode('-', $event);
        if ($entity instanceof Ticket) {
            $transports = $this->sourceConfigurator->getApiConfig($entity->source, 'transports', $type);
        } else {
            $transports = $this->sourceConfigurator->getApiConfig($entity->ticket->source, 'transports', $type);
        }
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
