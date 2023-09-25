<?php
declare(strict_types=1);

namespace App\Manager;

use App\Exceptions\ScheduledEventException;
use App\Factory\ScheduleTopicFactory;
use App\Models\ScheduleTopic;
use App\Models\Topic;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;

class ScheduleTopicManager
{
    /**
     * @var ScheduleTopicFactory
     */
    private $scheduleTopicFactory;

    /**
     * ScheduleTopic constructor.
     *
     * @param ScheduleTopicFactory $scheduleTopicFactory
     */
    public function __construct(ScheduleTopicFactory $scheduleTopicFactory)
    {
        $this->scheduleTopicFactory = $scheduleTopicFactory;
    }

    /**
     * @param Topic $topic
     * @param DateTime $occursAt
     *
     * @param User|null $user
     *
     * @return ScheduleTopic
     * @throws ScheduledEventException
     */
    public function createScheduleTopic(Topic $topic, DateTime $occursAt, User $user): ScheduleTopic
    {
        if (!$topic->has_practice) {
            throw new ScheduledEventException('Could not schedule event with non practice topic');
        }

        $scheduleTopic = $this->scheduleTopicFactory->create($user, $topic, $occursAt);

        $scheduleTopic->save();

        return $scheduleTopic;
    }

    /**
     * @param ScheduleTopic $scheduledTopic
     */
    public function nextTime(ScheduleTopic $scheduledTopic): void
    {
        $scheduledTopic->occurs_at = Carbon::now()->addMinutes(20);
        $scheduledTopic->save();
    }

    /**
     * @param ScheduleTopic $scheduleEvent
     */
    public function setSentAt(ScheduleTopic $scheduleEvent): void
    {
        $scheduleEvent->sent_at = Carbon::now();
        $scheduleEvent->save();
    }

    /**
     * @param ScheduleTopic $scheduledEvent
     *
     * @throws Exception
     */
    public function remove(ScheduleTopic $scheduledEvent): void
    {
        $scheduledEvent->delete();
    }
}
