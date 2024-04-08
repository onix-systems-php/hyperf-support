<?php

namespace OnixSystemsPHP\HyperfSupport\Resource\SupportUser;

use OnixSystemsPHP\HyperfCore\Resource\AbstractResource;
use OnixSystemsPHP\HyperfSupport\Contract\SupportUserInterface;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'SupportUserResource', properties: [
    new OA\Property(property: 'id', type: 'string'),
    new OA\Property(property: 'first_name', type: 'string'),
    new OA\Property(property: 'last_name', type: 'string'),
    new OA\Property(property: 'username', type: 'string'),
    new OA\Property(property: 'email', type: 'string'),
], type: 'object')]

/**
 * @method __construct(SupportUserInterface $resource)
 * @property SupportUserInterface $resource
 */
class SupportUserResource extends AbstractResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->resource->getId(),
            'email' => $this->resource->getEmail(),
            'first_name' => $this->resource->getFirstName(),
            'last_name' => $this->resource->getLastName(),
            'username' => $this->resource->getUsername(),
        ];
    }
}
