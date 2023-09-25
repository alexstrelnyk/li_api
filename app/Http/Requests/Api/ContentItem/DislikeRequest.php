<?php

namespace App\Http\Requests\Api\ContentItem;

use App\Http\Requests\ApiRequest;

class DislikeRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'input' => 'required'
        ];
    }

    /**
     * @return string
     */
    public function getInput(): string
    {
        return $this->get('input');
    }
}
