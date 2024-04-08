<?php

namespace OnixSystemsPHP\HyperfSupport\Resource\User;

use OnixSystemsPHP\HyperfCore\Resource\AbstractResource;
use OnixSystemsPHP\HyperfSupport\Contract\SupportUserInterface;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'UserResource', properties: [
    new OA\Property(property: 'email', type: 'integer'),
    new OA\Property(property: 'first_name', type: 'string'),
    new OA\Property(property: 'last_name', type: 'string'),
    new OA\Property(property: 'full_name', type: 'string'),
    new OA\Property(property: 'role', type: 'string'),
], type: 'object')]

/**
 * @method __construct(SupportUserInterface $resource)
 * @property SupportUserInterface $resource
 */
class UserResource extends AbstractResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->resource->getId(),
            'email' => $this->resource->getEmail(),
            'first_name' => $this->resource->getFirstName(),
            'last_name' => $this->resource->getLastName(),
            'full_name' => $this->resource->getUsername(),
        ];
    }
}
