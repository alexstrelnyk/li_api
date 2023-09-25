<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Topic;

use App\Http\Requests\ApiRequest;
use App\Models\Topic;
use App\Rules\StatusRule;

class UpdatePatchRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'string',
            'focus_id' => 'exists:focuses,id',
            'has_practice' => 'boolean',
            'content_item_id' => 'required_if:has_practice,true',
            'introduction' => 'string',
            'calendar_prompt_text' => 'required_if:has_practice,true|string',
            'status' => new StatusRule(new Topic()),
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'focus_id.exists' => 'Focus with passed id not found',
        ];
    }
}
