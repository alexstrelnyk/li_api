<?php
declare(strict_types=1);

namespace App\Console\Commands\Send;

use App\Manager\ContentItemManager;
use App\Manager\ScheduleTopicManager;
use App\Models\ScheduleTopic;
use App\Models\SystemSettings;
use App\Models\User;
use App\Notifications\PracticeCoachingSessionNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendPushNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check scheduled events and send notifications';

    /**
     * @var ScheduleTopicManager
     */
    private $scheduleTopicManager;

    /**
     * SendPushNotificationCommand constructor.
     *
     * @param ScheduleTopicManager $scheduleTopicManager
     */
    public function __construct(ScheduleTopicManager $scheduleTopicManager)
    {
        parent::__construct();
        $this->scheduleTopicManager = $scheduleTopicManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        $this->output->writeln(sprintf('Current time: %s', Carbon::now()));

        $scheduledEvents = ScheduleTopic::whereHas('user', static function (Builder $builder) {
                /** @var User $builder */
                $builder
                    ->practiceNotificationsOn()
                    ->withRegisteredDevices();
            })
            ->where(static function (Builder $builder) {
                $builder->where(static function (Builder $builder) {
                    /** @var ScheduleTopic $builder */
                    $builder
                        ->primerNotSent()
                        ->where('created_at', '<=', Carbon::now()->addMinutes((int) SystemSettings::getByKey('primer_notification_time')->value));
                })->orWhere(static function (Builder $builder) {
                    /** @var ScheduleTopic $builder */
                    $builder
                        ->primerSent()
                        ->contentNotSent()
                        ->where('occurs_at', '<=', Carbon::now()->addMinutes((int) SystemSettings::getByKey('content_notification_time')->value));
                })->orWhere(static function (Builder $builder) {
                    /** @var ScheduleTopic $builder */
                    $builder
                        ->contentPracticeBasedItemHasReflection()
                        ->primerSent()
                        ->contentSent()
                        ->reflectionNotSent()
                        ->laterThan(Carbon::now()->subMinutes((int) SystemSettings::getByKey('reflection_notification_time')->value));
                });
            })
            ->whereHas('topic', static function (Builder $builder) {
                $builder->where('has_practice', true)->whereHas('contentItem');
            })
            ->get();

        foreach ($scheduledEvents as $scheduledEvent) {
            /** @var ScheduleTopic $scheduledEvent */
            $user = $scheduledEvent->user;

            $remove = false;
            if ($scheduledEvent->primer_sent_at === null) {
                $screen = ContentItemManager::PRIMER_VIEWED_TYPE;
            } elseif ($scheduledEvent->primer_sent_at !== null && $scheduledEvent->content_sent_at === null) {
                $screen = ContentItemManager::CONTENT_VIEWED_TYPE;
                if (!$scheduledEvent->topic->contentItem->has_reflection) {
                    $remove = true;
                }
            } elseif ($scheduledEvent->primer_sent_at !== null && $scheduledEvent->content_sent_at !== null && $scheduledEvent->reflection_sent_at === null) {
                $screen = ContentItemManager::REFLECTION_VIEWED_TYPE;
                $remove = true;
            }

//            $progress = ContentItemUserProgress::ofUserAndItem($user, $scheduledEvent->topic->contentItem)->first();
//
//            if ($progress instanceof ContentItemUserProgress) {
//                $reflectionCondition = $scheduledEvent->topic->contentItem->has_reflection === false
//                    || ($scheduledEvent->topic->contentItem->has_reflection === true && $progress->reflection);
//
//                if ($progress->primer && $progress->content && $reflectionCondition) {
//                    continue;
//                }
//            }

            $user->notify(new PracticeCoachingSessionNotification($scheduledEvent, $screen));

            if ($remove) {
                $this->scheduleTopicManager->remove($scheduledEvent);
            }

            $this->output->writeln(sprintf('Notification requested for sending to %s user', $user->email));
        }

        return 0;
    }
}
