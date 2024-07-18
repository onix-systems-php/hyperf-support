<?php

declare(strict_types=1);

namespace OnixSystemsPHP\HyperfSupport\Request\Tickets;

use Hyperf\Validation\Request\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateTicketRequest',
    properties: [
        new OA\Property(property: 'source', type: 'string'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'content', type: 'string'),
        new OA\Property(property: 'custom_fields', type: 'object', example: '{"type": "Feature Request"}'),
        new OA\Property(property: 'page_url', type: 'string'),
        new OA\Property(property: 'files', type: 'array', items: new OA\Items(
            ref: '#/components/schemas/RequestFileAssign'
        )),
    ],
    type: 'object',
)]
class CreateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'source' => 'required|string',
            'title' => 'required|min:5|max:255',
            'content' => 'required|min:20',
            'custom_fields' => 'present|array',
            'page_url' => 'required|url:https',
            'files' => 'array'
        ];
    }
}
