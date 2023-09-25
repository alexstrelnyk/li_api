<?php

namespace App\Http\Requests\Api\Activity;

use App\Http\Requests\ApiRequest;
use App\Models\User;
use App\Rules\ActivityEventRule;
use App\Services\SilScoreService\SilScoreService;

/**
 * Class CreateActivityRequest
 * @package App\Http\Requests\Api\Activity
 */
class CreateActivityRequest extends ApiRequest
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'event_name' => ['required', new ActivityEventRule(SilScoreService::getAvailableSilScoreEvents())]
        ];
    }
}