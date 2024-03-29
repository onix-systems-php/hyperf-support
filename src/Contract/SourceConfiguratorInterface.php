<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Contract;

interface SourceConfiguratorInterface
{
    /**
     * Get api config for the given source with config keys.
     *
     * @param string $source
     * @param string ...$keys
     * @return mixed
     */
    public function getApiConfig(string $source, string ...$keys): mixed;

    /**
     * Get source depending on integration name and integration key.
     *
     * @param string $integration
     * @param string $key
     * @return string|null
     */
    public function getConfigValueByIntegrationAndKey(string $integration, string $key): ?string;

    /**
     * Check if the given integration key is valid or not.
     *
     * @param string $integration
     * @param string $key
     * @return bool
     */
    public function isValidApiKey(string $integration, string $key): bool;
}
