<?php

declare(strict_types=1);

namespace OnixSystemsPHP\HyperfSupport\Request\Comments;

use Hyperf\Validation\Request\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateCommentRequest',
    properties: [
        new OA\Property(property: 'ticket_id', type: 'integer'),
        new OA\Property(property: 'content', type: 'string'),
        new OA\Property(property: 'files', type: 'array', items: new OA\Items(
            ref: '#/components/schemas/RequestFileAssign'
        )),
    ],
    type: 'object',
)]
class CreateCommentRequest extends FormRequest
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
            'ticket_id' => 'required|integer|exists:tickets,id',
            'content' => 'required|string',
            'files' => 'array'
        ];
    }
}
