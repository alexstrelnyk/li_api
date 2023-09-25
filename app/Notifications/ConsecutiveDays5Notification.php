<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Channel\AzureNotificationHubChannel;
use App\DataTransferObjects\PushNotificationDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConsecutiveDays5Notification extends Notification
{
    use Queueable;

    /**
     * @var int
     */
    private $countDays;

    /**
     * Create a new notification instance.
     *
     * @param int $countDays
     */
    public function __construct(int $countDays)
    {
        $this->countDays = $countDays;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [AzureNotificationHubChannel::class];
    }

    /**
     * @param $notifiable
     *
     * @return PushNotificationDTO
     */
    public function toAzureNotificationHub($notifiable): PushNotificationDTO
    {
        return new PushNotificationDTO(
            sprintf('%s consecutive days enganged', $this->countDays),
            sprintf('You recently received a bonus for using the app for %s consecutive days. Jump back in to continue.', $this->countDays)
        );
    }
}
