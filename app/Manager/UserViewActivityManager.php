<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\UserViewActivityFactory;
use App\Models\Activity;
use App\Models\User;
use App\Models\UserViewedActivity;

class UserViewActivityManager
{
    /**
     * @var UserViewActivityFactory
     */
    private $userViewActivityFactory;

    /**
     * UserViewActivityManager constructor.
     *
     * @param UserViewActivityFactory $userViewActivityFactory
     */
    public function __construct(UserViewActivityFactory $userViewActivityFactory)
    {
        $this->userViewActivityFactory = $userViewActivityFactory;
    }

    /**
     * @param User $user
     * @param Activity $activity
     *
     * @return UserViewedActivity
     */
    public function create(User $user, Activity $activity): UserViewedActivity
    {
        $userViewActivity = $this->userViewActivityFactory->create($user, $activity);
        $userViewActivity->save();

        return $userViewActivity;
    }
}
