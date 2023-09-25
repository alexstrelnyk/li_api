<?php
declare(strict_types=1);

namespace App\Transformers\ScheduleTopic;

use App\Models\ScheduleTopic;
use App\Transformers\AbstractTransformer;
use Exception;

class ScheduleTopicTransformer extends AbstractTransformer implements ScheduleTopicTransformerInterface
{
    /**
     * @param ScheduleTopic $scheduleTopic
     *
     * @return array
     * @throws Exception
     */
    public function transform(ScheduleTopic $scheduleTopic): array
    {
        return [
            'id' => $scheduleTopic->id,
            'user_id' => $scheduleTopic->user_id,
            'topic_id' => $scheduleTopic->topic_id,
            'occurs_at' => $this->date($scheduleTopic->occurs_at)
        ];
    }
}
