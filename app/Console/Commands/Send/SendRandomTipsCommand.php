<?php
declare(strict_types=1);

namespace App\Console\Commands\Send;

use App\Manager\UserManager;
use App\Models\ContentItem;
use App\Models\User;
use App\Notifications\TipNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendRandomTipsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:tips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and send random tips to users';

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * Create a new command instance.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        parent::__construct();
        $this->userManager = $userManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->output->writeln(sprintf('Current time : %s', Carbon::now()));
        $users = User::randomActsNotificationsOn()
            ->withRegisteredDevices()
            ->where('permission', User::APP_USER)
            ->where(static function (Builder $builder) {
                $builder->whereNull('tip_time')->when(Carbon::now()->setTimeFromTimeString(User::DEFAULT_TIP_TIME) > Carbon::now(), static function (Builder $builder) {
                    $builder->whereNull('id');
                });
                $builder->orWhereTime('tip_time', '<=', Carbon::now()->format('H:i:s'));
            })
            ->where(static function (Builder $builder) {
                $builder->whereNull('last_tip_at')->orWhereDate('last_tip_at', '<', Carbon::now()->startOfDay());
            })
            ->get();

        /** @var User $user */
        foreach ($users as $user) {
            $viewedTipsIds = $user->viewedTips()->pluck('id')->toArray();

            $tip = ContentItem::tip()->whereNotIn('id', $viewedTipsIds)->whereNotNull('topic_id')->first();

            if ($tip instanceof ContentItem) {
                $user->notify(new TipNotification($tip));
                $this->output->writeln(sprintf('Tip %s sent to %s user', $tip->id, $user->email));
                $this->userManager->updateLastTipAt($user);
            } else {
                $this->output->writeln(sprintf('Nothing send to %s user', $user->email));
            }
        }

        return 0;
    }
}
