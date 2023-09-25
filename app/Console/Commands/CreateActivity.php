<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Focus;
use App\Models\User;
use App\Services\Activity\ActivityService;
use App\Services\Activity\Types\ActivityInterface;
use App\Services\Activity\Types\WrittenReflectionActivity;
use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Carbon;

class CreateActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:activity:create {type_activity} {model_class} {model_id} {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test creation activity';

    /**
     * @var ActivityService
     */
    private $activityService;

    /**
     * @var Application
     */
    private $application;

    /**
     * CreateActivity constructor.
     * @param ActivityService $activityService
     * @param Application $application
     */
    public function __construct(ActivityService $activityService, Application $application)
    {
        parent::__construct();
        $this->activityService = $activityService;
        $this->application = $application;
    }

    /**
     * @return string
     * @throws \App\Exceptions\ActivityNotFoundException
     */
    public function handle()
    {
        $typeActivity = $this->argument('type_activity');
        $modelClass =  'App\Models\\' . $this->argument('model_class');
        $modelId = $this->argument('model_id');
        $userId = $this->argument('user_id');

        $class = $this->activityService->getActivityClass($typeActivity);

        $user = User::find($userId);

        $activityClass = $this->application->get($class);

        /** @var ActivityInterface $activity */
        $activity = new $activityClass();

        $activity->setUser($user);
        $activity->setPayloads([['class' => $modelClass, 'class_id' => (int)$modelId]]);

        $activity->setDate(Carbon::now());

        $this->activityService->createActivity($activity, $user);
    }
}
