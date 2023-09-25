<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\Activity;
use App\Models\User;
use App\Models\UserViewedActivity;

class UserViewActivityFactory
{
    /**
     * @param User $user
     * @param Activity $activity
     *
     * @return UserViewedActivity
     */
    public function create(User $user, Activity $activity): UserViewedActivity
    {
        $userViewActivity = new UserViewedActivity();
        $userViewActivity->user()->associate($user);
        $userViewActivity->activity()->associate($activity);

        return $userViewActivity;
    }
}
