<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Channel\AzureNotificationHubChannel;
use App\Channel\DatabaseChannel;
use App\DataTransferObjects\PushNotificationDTO;
use App\Notifications\Interfaces\StorableNotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LastCompletedCoachingSessionNotification extends Notification implements StorableNotificationInterface
{
    use Queueable;

    /**
     * @var int
     */
    private $days;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string
     */
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param int $days
     * @param string|null $title
     */
    public function __construct(int $days, ?string $title = null)
    {
        $this->days = $days;
        $this->title = $title ?? 'Try another coaching session';
        $this->message = sprintf('%s days since last completed coaching session', $this->days);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'last-completed-coaching-session:'.$this->days;
    }

    /**
     * @return array
     */
    public function via(): array
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
        return new PushNotificationDTO(
            $this->title,
            $this->message,
            [
                'action' => json_encode([
                    'screen' => 'Focuses',
                    'params' => []
                ], JSON_THROW_ON_ERROR, 512)
            ]
        );
    }

    /**
     * @param $notifiable
     *
     * @return array
     */
    public function toDatabase($notifiable): array
    {
        return [
            'day' => $this->days,
            'type' => $this->getType(),
            'title' => $this->title,
            'message' => $this->message
        ];
    }
}
