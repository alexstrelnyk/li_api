<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\ContentItem;

use App\Http\Requests\ApiRequest;
use App\Manager\ContentItemManager;

class ViewedRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:'.implode(',', ContentItemManager::getAvailableViewedTypes())
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'type.in' => 'Invalid type. Available types ['.implode(',', ContentItemManager::getAvailableViewedTypes()).']'
        ];
    }
}
