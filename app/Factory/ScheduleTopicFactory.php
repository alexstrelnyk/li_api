<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\ScheduleTopic;
use App\Models\Topic;
use App\Models\User;
use DateTime;

class ScheduleTopicFactory
{
    /**
     * @param User $user
     * @param Topic $topic
     * @param DateTime $occursAt
     *
     * @return ScheduleTopic
     */
    public function create(User $user, Topic $topic, DateTime $occursAt): ScheduleTopic
    {
        $scheduleTopic = new ScheduleTopic();
        $scheduleTopic->user()->associate($user);
        $scheduleTopic->topic()->associate($topic);
        $scheduleTopic->occurs_at = $occursAt;

        return $scheduleTopic;
    }
}
