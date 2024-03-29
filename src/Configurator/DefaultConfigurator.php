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
    public function __construct(private ConfigInterface $config) {}

    /**
     * @inheritDoc
     */
    public function getApiConfig(string $source, string ...$keys): mixed
    {
        // TODO: add 'integrations' where this method is used for integrations
        if ($source) {
            return $this->config->get('support.' . implode('.', $keys));
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getConfigValueByIntegrationAndKey(string $integration, string $key): ?string
    {
        if ($integration === 'trello') {
            return $this->config->get('support.integrations.' . $integration . '.' . $key);
        }
        if ($integration === 'slack') {
            return $this->config->get('support.integrations.' . $integration . '.' . $key);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function isValidApiKey(string $integration, string $key): bool
    {
        $source = match($integration) {
            'trello' => $this->config->get('support.integrations.trello' . '.keys_to_source.' . $key),
            'slack' => $this->config->get('support.integrations.slack' . '.keys_to_source.' . $key),
            default => null,
        };

        return !is_null($source);
    }
}
