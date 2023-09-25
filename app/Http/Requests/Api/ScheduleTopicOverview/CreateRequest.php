<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\ScheduleTopicOverview;

use App\Http\Requests\ApiRequest;

class CreateRequest extends ApiRequest
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'occurs_at' => 'required|date_format:'.self::DATE_TIME_FORMAT,
            'user_id' => 'exists:users,id',
            'topic_id' => 'required|integer|exists:topics,id'
        ];
    }
}
