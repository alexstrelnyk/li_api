<?php
declare(strict_types=1);

namespace App\Manager;

use App\Exceptions\ActivityNotFoundException;
use App\Models\Activity;
use App\Models\User;
use App\Services\Activity\ActivityService;

class ActivityManager
{
    /**
     * @var ActivityService
     */
    public $activityService;
    /**
     * @var UserViewActivityManager
     */
    private $userViewActivityManager;

    /**
     * ActivityManager constructor.
     *
     * @param ActivityService $activityService
     * @param UserViewActivityManager $userViewActivityManager
     */
    public function __construct(ActivityService $activityService, UserViewActivityManager $userViewActivityManager)
    {
        $this->activityService = $activityService;
        $this->userViewActivityManager = $userViewActivityManager;
    }

    /**
     * @param string $type
     * @param User $user
     * @param array $payloads
     * @throws ActivityNotFoundException
     */
    public function createActivity(string $type, User $user, array $payloads): void
    {
        $this->activityService->createActivity($type, $user, $payloads);
    }

    /**
     * @param Activity $activity
     * @param User $user
     */
    public function viewActivity(Activity $activity, User $user): void
    {
        $this->userViewActivityManager->create($user, $activity);
    }

    /**
     * @param Activity[] $activities
     * @param User $user
     */
    public function viewActivities($activities, User $user): void
    {
        foreach ($activities as $activity) {
            $this->viewActivity($activity, $user);
        }
    }
}
