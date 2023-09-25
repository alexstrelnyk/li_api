<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Channel\AzureNotificationHubChannel;
use App\DataTransferObjects\PushNotificationDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventsScheduledNotification extends Notification
{
    use Queueable;
    /**
     * @var int
     */
    private $days;

    /**
     * Create a new notification instance.
     *
     * @param int $days
     */
    public function __construct(int $days)
    {
        $this->days = $days;
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
            sprintf('Quick Tip: Inclusive %s. Tap to read.', $this->days),
            [
                'action' => json_encode([
                    'screen' => 'Topics',
                    'params' => []
                ], JSON_THROW_ON_ERROR, 512)
            ]
        );
    }
}
