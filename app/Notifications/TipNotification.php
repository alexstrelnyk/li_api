<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Channel\AzureNotificationHubChannel;
use App\DataTransferObjects\PushNotificationDTO;
use App\Models\ContentItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TipNotification extends Notification
{
    use Queueable;

    /**
     * @var ContentItem
     */
    private $contentItem;

    /**
     * Create a new notification instance.
     *
     * @param ContentItem $contentItem
     */
    public function __construct(ContentItem $contentItem)
    {
        $this->contentItem = $contentItem;
    }

    /**
     * @param $notifiable
     *
     * @return array
     */
    public function via($notifiable): array
    {
        return [AzureNotificationHubChannel::class];
    }

    /**
     * @param $notifiable
     *
     * @return PushNotificationDTO
     */
    public function toAzureNotificationHubChanel($notifiable): PushNotificationDTO
    {
        return new PushNotificationDTO(
            'Random Act of Inclusion',
            sprintf('Quick Tip: Inclusive %s. Tap to read.', $this->contentItem->tipTopic->title),
            [
                'action' => json_encode([
                    'screen' => 'Tip',
                    'params' => ['id' => $this->contentItem->id]
                ], JSON_THROW_ON_ERROR, 512)
            ]
        );
    }
}
