<?php
declare(strict_types=1);

namespace App\Console\Commands\Send;

use App\Models\User;
use App\Notifications\ConsecutiveDays4Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendConsecutiveDays4EngangedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:consecutive-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications about consecutive days';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $countDays = 4;
        $users = User::randomActsNotificationsOn()
            ->where('permission', User::APP_USER)
            ->whereTime('tip_time', '<=', Carbon::now()->format('H:i:s'))
            ->where(User::getConsecutiveDaysKey(), $countDays + 1)
            ->where(User::getLastSeenKey(), '>=', Carbon::now()->subDays($countDays + 1))
            ->get()
        ;

        $users->each(static function (User $user) use ($countDays) {
            $user->notify(new ConsecutiveDays4Notification($countDays));
        });

        return 0;
    }
}
