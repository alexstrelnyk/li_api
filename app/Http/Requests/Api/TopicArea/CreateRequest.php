<?php

namespace App\Http\Requests\Api\TopicArea;

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
            'focus_area_id' => 'required|exists:focus_area,id',
            'topic_id' => 'required|exists:topics,id'
        ];
    }

    /**
     * @return int
     */
    public function getFocusAreaId(): int
    {
        return $this->get('focus_area_id');
    }

    /**
     * @return int
     */
    public function getTopicId(): int
    {
        return $this->get('topic_id');
    }
}
