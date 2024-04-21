<?php

declare(strict_types=1);

namespace OnixSystemsPHP\HyperfSupport\Request;

use Hyperf\Validation\Request\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateCommentRequest',
    properties: [
        new OA\Property(property: 'ticket_id', type: 'integer'),
        new OA\Property(property: 'content', type: 'string'),
        new OA\Property(property: 'from', type: 'string'),
        new OA\Property(property: 'source', type: 'string'),
        new OA\Property(property: 'creator_name', type: 'string'),
        new OA\Property(property: 'trello_comment_id', type: 'string'),
        new OA\Property(property: 'slack_comment_id', type: 'string'),
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
            'from' => 'nullable|string',
            'source' => 'nullable|string',
            'creator_name' => 'required|string',
            'trello_comment_id' => 'nullable|string',
            'slack_comment_id' => 'nullable|string',
            'files' => 'array'
        ];
    }
}
