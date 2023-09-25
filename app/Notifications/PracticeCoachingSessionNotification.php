<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Channel\AzureNotificationHubChannel;
use App\Channel\DatabaseChannel;
use App\DataTransferObjects\PushNotificationDTO;
use App\Manager\ContentItemManager;
use App\Models\ContentItemUserProgress;
use App\Models\ScheduleTopic;
use App\Notifications\Interfaces\StorableNotificationInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use LogicException;

class PracticeCoachingSessionNotification extends Notification implements StorableNotificationInterface
{
    use Queueable;
    /**
     * @var ScheduleTopic
     */
    private $scheduleTopic;

    /**
     * @var ContentItemUserProgress|null
     */
    private $contentItemUserProgress;

    /**
     * @var string
     */
    private $screen;

    /**
     * PracticeCoachingSessionNotification constructor.
     *
     * @param ScheduleTopic $scheduleTopic
     * @param string $screen
     * @param ContentItemUserProgress|null $contentItemUserProgress
     */
    public function __construct(ScheduleTopic $scheduleTopic, string $screen, ?ContentItemUserProgress $contentItemUserProgress = null)
    {
        $this->scheduleTopic = $scheduleTopic;
//        $this->contentItemUserProgress = $contentItemUserProgress;
        $this->screen = $screen;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'scheduled-event';
    }

    /**
     * @param $notifiable
     *
     * @return array
     */
    public function via($notifiable): array
    {
        return [AzureNotificationHubChannel::class, DatabaseChannel::class];
    }

    /**
     * @param $notifiable
     *
     * @return PushNotificationDTO
     */
    public function toAzureNotificationHubChanel($notifiable): PushNotificationDTO
    {
        $contentItem = $this->scheduleTopic->topic->contentItem;

        if ($this->screen === ContentItemManager::PRIMER_VIEWED_TYPE) {
            $notificationText = $contentItem->primer_notification_text ?? '';
            $this->scheduleTopic->primer_sent_at = Carbon::now();
        } elseif ($this->screen === ContentItemManager::CONTENT_VIEWED_TYPE) {
            $notificationText = $contentItem->content_notification_text ?? '';
            $this->scheduleTopic->content_sent_at = Carbon::now();
        } elseif ($this->screen === ContentItemManager::REFLECTION_VIEWED_TYPE) {
            $notificationText = $contentItem->reflection_notification_text ?? '';
            $this->scheduleTopic->reflection_sent_at = Carbon::now();
        } else {
            throw new LogicException('Can \'t detect the needed screen');
        }

//        if (!$this->contentItemUserProgress instanceof ContentItemUserProgress || !$this->contentItemUserProgress->primer) {
//            $screen = ContentItemManager::PRIMER_VIEWED_TYPE;
//            $notificationText = $contentItem->primer_notification_text ?? '';
//            $this->scheduleTopic->primer_sent_at = Carbon::now();
//        } elseif (!$this->contentItemUserProgress->content) {
//            $screen = ContentItemManager::CONTENT_VIEWED_TYPE;
//            $notificationText = $contentItem->content_notification_text ?? '';
//            $this->scheduleTopic->content_sent_at = Carbon::now();
//        } elseif ($contentItem->has_reflection && !$this->contentItemUserProgress->reflection) {
//            $screen = ContentItemManager::REFLECTION_VIEWED_TYPE;
//            $notificationText = $contentItem->reflection_notification_text ?? '';
//            $this->scheduleTopic->reflection_sent_at = Carbon::now();
//        } else {
//            throw new LogicException('Can \'t detect the needed screen');
//        }

        $this->scheduleTopic->save();

        return new PushNotificationDTO($this->scheduleTopic->topic->title, $notificationText, [
            'action' => json_encode([
                'screen' => $this->screen,
                'params' => [
                    'id' => $contentItem->id
                ]
            ], JSON_THROW_ON_ERROR, 512)
        ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => $this->getType()
        ];
    }

}
