<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport;

use OnixSystemsPHP\HyperfSupport\Configurator\DefaultConfigurator;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Contract\TicketDescriptionGeneratorContract;
use OnixSystemsPHP\HyperfSupport\Generator\DefaultTicketDescriptionGenerator;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                SourceConfiguratorInterface::class => DefaultConfigurator::class,
                TicketDescriptionGeneratorContract::class => DefaultTicketDescriptionGenerator::class,
            ],
            'commands' => [],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'migration_tickets_table',
                    'description' => 'The addition for migration from onix-systems-php/hyperf-support.',
                    'source' => __DIR__ . '/../publish/migrations/2024_02_07_105940_create_tickets_table.php',
                    'destination' => BASE_PATH . '/migrations/2024_02_07_105940_create_tickets_table.php',
                ],
                [
                    'id' => 'migration_comments_table',
                    'description' => 'The addition for migration from onix-systems-php/hyperf-support.',
                    'source' => __DIR__ . '/../publish/migrations/2024_02_07_105947_create_comments_table.php',
                    'destination' => BASE_PATH . '/migrations/2024_02_07_105947_create_comments_table.php',
                ],
                [
                    'id' => 'support_config',
                    'description' => 'The config for onix-systems-php/hyperf-support.',
                    'source' => __DIR__ . '/../publish/config/support.php',
                    'destination' => BASE_PATH . '/config/autoload/support.php',
                ],
            ],
        ];
    }
}
