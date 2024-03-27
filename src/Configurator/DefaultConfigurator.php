<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Configurator;

use Hyperf\Contract\ConfigInterface;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;

readonly class DefaultConfigurator implements SourceConfiguratorInterface
{
    public function __construct(private ConfigInterface $config)
    {
    }

    /**
     * @inheritDoc
     */
    public function getApiConfig(string $source, string ...$keys): mixed
    {
        if ($source) {
            return $this->config->get('support.' . implode('.', $keys));
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSourceByIntegrationAndKey(string $integration, string $key): ?string
    {
        if ($integration === 'trello') {
            return $this->config->get('support.keys_to_source.trello.' . $key);
        }
        if ($integration === 'slack') {
            return $this->config->get('support.keys_to_source.slack.' . $key);
        }

        return null;
    }
}
