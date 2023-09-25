<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\UserFeedback;

use App\Http\Requests\ApiRequest;

class CreateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'content_item_id' => 'required|exists:content_items,id',
            'reaction' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'content_item_id:exists' => 'Content Item with passed id not found'
        ];
    }
}
