<?php

namespace App\Http\Requests\Api\Setting;

use App\Http\Requests\ApiRequest;

class EditRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'tip_time' => 'string',
            'event_practice_email' => 'boolean',
            'event_practice_notification' => 'boolean',
            'random_acts_email' => 'boolean',
            'random_acts_notification' => 'boolean',
            'login_streak_email' => 'boolean',
            'login_streak_notification' => 'boolean',
            'share_activity' => 'boolean'
        ];
    }
}
