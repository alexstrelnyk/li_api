<?php
declare(strict_types=1);

namespace App\Console\Commands\Send;

use App\Manager\NotificationManager;
use Illuminate\Console\Command;

class LastCompletedCoachingSessionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:last-coaching-session';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It has been 5/15/30 days since you last completed a coaching session. Click here to get some new tips and continue practicing!';

    /**
     * @var NotificationManager
     */
    private $notificationManager;

    /**
     * Create a new command instance.
     *
     * @param NotificationManager $notificationManager
     */
    public function __construct(NotificationManager $notificationManager)
    {
        parent::__construct();
        $this->notificationManager = $notificationManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $this->notificationManager->sendLastCoachingSessionNotifications();

        return 0;
    }
}
