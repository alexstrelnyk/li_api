<?php
declare(strict_types=1);

namespace App\Console\Commands\Send;

use App\Models\User;
use App\Notifications\ConsecutiveDays5Notification;
use App\Services\SilScoreService\Types\ConsecutiveDays5SilScore;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendConsecutiveDays5EngangedCommand extends Command
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
        $countDays = 5;
        $users = User::randomActsNotificationsOn()
            ->whereHas('silScores', static function (Builder $builder) {
                $builder->where('type', ConsecutiveDays5SilScore::getType())
                    ->whereDate('created_at', Carbon::now()->addDay());
            })
            ->where('permission', User::APP_USER)
            ->whereTime('tip_time', '<=', Carbon::now()->format('H:i:s'))
            ->where(User::getConsecutiveDaysKey(), $countDays + 1)
            ->where(User::getLastSeenKey(), '>=', Carbon::now()->subDays($countDays))
            ->get()
        ;

        $users->each(static function (User $user) use ($countDays) {
            $user->notify(new ConsecutiveDays5Notification($countDays));
        });

        return 0;
    }
}
