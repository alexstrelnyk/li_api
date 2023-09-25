<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\TopicArea;

use App\Http\Requests\ApiRequest;

class DetachContentItemRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'content_item_id' => 'required|exists:content_items,id'
        ];
    }

    /**
     * @return int
     */
    public function getContentItemId(): int
    {
        return $this->get('content_item_id');
    }
}
