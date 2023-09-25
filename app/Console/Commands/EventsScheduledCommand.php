<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ScheduleTopic;
use App\Notifications\EventsScheduledNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EventsScheduledCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:event-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification about 2 days away event';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $events = ScheduleTopic::laterThan(Carbon::now()->addDays(2))->get();

        $events->each(static function (ScheduleTopic $event) {
            $event->user->notify(new EventsScheduledNotification(2));
        });

        return 0;
    }
}
