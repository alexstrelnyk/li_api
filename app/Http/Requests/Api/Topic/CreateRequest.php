<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Topic;

use App\Http\Requests\ApiRequest;
use App\Models\Topic;
use App\Rules\StatusRule;

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
            'title' => 'required|string',
            'focus_id' => 'required|exists:focuses,id',
            'content_item_id' => 'required_if:has_practice,true|exists:content_items,id',
            'has_practice' => 'required|boolean',
            'introduction' => 'required|string',
            'calendar_prompt_text' => 'required_if:has_practice,true',
            'status' => ['required', new StatusRule(new Topic())]
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'focus_id.exists' => 'Focus with passed ID not found',
            'content_item_id.exists' => 'Content Item with passed ID not found'
        ];
    }

    /**
     * @return bool
     */
    public function getHasPractice(): bool
    {
        return $this->get('has_practice');
    }

    /**
     * @return int
     */
    public function getFocusId(): int
    {
        return $this->get('focus_id');
    }

    /**
     * @return int
     */
    public function getContentItemId(): int
    {
        return $this->get('content_item_id');
    }
}
