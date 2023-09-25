<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Topic;

use App\Http\Requests\ApiRequest;
use App\Models\Topic;
use App\Rules\StatusRule;

class UpdateRequest extends ApiRequest
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
            'has_practice' => 'required|boolean',
            'content_item_id' => 'required_if:has_practice,true',
            'introduction' => 'required|string',
            'calendar_prompt_text' => 'required_if:has_practice,true',
            'status' => ['required', new StatusRule(new Topic())],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required'    => 'A title key is required',
            'slug.required'     => 'Slug is missing',
            'focus_id.required' => 'A focus_id key is required',
        ];
    }
}
