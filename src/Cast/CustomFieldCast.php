<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Cast;

use Hyperf\Contract\CastsAttributes;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

class CustomFieldCast implements CastsAttributes
{
    /**
     * @param $model
     * @param string $key
     * @param $value
     * @param array $attributes
     * @return array
     */
    public function get($model, string $key, $value, array $attributes): array
    {
        if (empty($attributes['custom_fields'])) {
            return [];
        }

        return json_decode($attributes['custom_fields'], true);
    }

    /**
     * @param $model
     * @param string $key
     * @param $value
     * @param array $attributes
     * @return array
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        /** @var Ticket $model */
        if (!empty($model->custom_fields)) {
            return $this->updateCustomFields($model, $value);
        }

        return ['custom_fields' => json_encode($value)];
    }

    /**
     * Update Custom Fields on model.
     *
     * @param Ticket $model
     * @param array $customFields
     * @return array
     */
    private function updateCustomFields(Ticket $model, array $customFields): array
    {
        return ['custom_fields' => json_encode(array_merge($model->custom_fields, $customFields))];
    }
}
