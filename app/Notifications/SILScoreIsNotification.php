<?php
declare(strict_types=1);

namespace App\Notifications;

use App\DataTransferObjects\PushNotificationDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SILScoreIsNotification extends Notification
{
    use Queueable;

    /**
     * @var int
     */
    private $points;

    /**
     * Create a new notification instance.
     *
     * @param int $points
     */
    public function __construct(int $points)
    {
        $this->points = $points;
    }

    /**
     * @param $notifiable
     *
     * @return PushNotificationDTO
     */
    public function toAzureNotificationHubChanel($notifiable): PushNotificationDTO
    {
        return new PushNotificationDTO(
            'Congratulations!',
            sprintf('Your SIL score has increased to %s! Keep up the great work. ', $this->points),
            [
                'action' => json_encode([
                    'screen' => 'Profile',
                    'params' => []
                ], JSON_THROW_ON_ERROR, 512)
            ]
        );
    }
}
