<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Manager\UserReflectionManager;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class RewriteOnboardingReflectionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:onboarding-reflections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var UserReflectionManager
     */
    private $userReflectionManager;

    /**
     * Create a new command instance.
     *
     * @param UserReflectionManager $userReflectionManager
     */
    public function __construct(UserReflectionManager $userReflectionManager)
    {
        parent::__construct();
        $this->userReflectionManager = $userReflectionManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        User::whereNotNull('onboarding_reflection')->whereDoesntHave('reflections', static function (Builder $builder) {
            $builder->whereNull('content_item_id');
        })->get()->each(function (User $user) {
            $reflectionData = json_decode($user->onboarding_reflection, true, 512, JSON_THROW_ON_ERROR);
            $userReflection = $this->userReflectionManager->crateOnboardingReflection($user, $reflectionData['text']);
            $userReflection->created_at = Carbon::createFromTimestamp($reflectionData['created_at']);
            $userReflection->save();
        });

        return 0;
    }
}
