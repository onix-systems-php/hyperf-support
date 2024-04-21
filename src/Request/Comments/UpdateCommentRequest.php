<?php

declare(strict_types=1);

namespace OnixSystemsPHP\HyperfSupport\Request\Comments;

use Hyperf\Validation\Request\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateCommentRequest',
    properties: [
        new OA\Property(property: 'content', type: 'string'),
        new OA\Property(property: 'slack_id', type: 'string'),
        new OA\Property(property: 'trello_id', type: 'string'),
        new OA\Property(property: 'files', type: 'array', items: new OA\Items(
            ref: '#/components/schemas/RequestFileAssign'
        )),
    ],
    type: 'object',
)]
class UpdateCommentRequest extends FormRequest
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
            'content' => 'required|min:5|string',
            'trello_id' => 'string|nullable',
            'slack_id' => 'string|nullable',
            'files' => 'array|nullable'
        ];
    }
}
