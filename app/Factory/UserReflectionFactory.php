<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\ContentItem;
use App\Models\ContentItemUserProgress;
use App\Models\Program;
use App\Models\User;
use App\Models\UserReflection;

class UserReflectionFactory
{
    /**
     * @param User $user
     * @param ContentItem $contentItem
     * @param Program $program
     *
     * @param ContentItemUserProgress $userProgress
     *
     * @return UserReflection
     */
    public function create(User $user, ContentItem $contentItem, Program $program, ContentItemUserProgress $userProgress): UserReflection
    {
        $userReflection = new UserReflection();
        $userReflection->user()->associate($user);
        $userReflection->program()->associate($program);
        $userReflection->contentItem()->associate($contentItem);
        $userReflection->userProgress()->associate($userProgress);

        return $userReflection;
    }

    /**
     * @param User $user
     *
     * @return UserReflection
     */
    public function createOnboardingReflection(User $user): UserReflection
    {
        $userReflection = new UserReflection();
        $userReflection->user()->associate($user);

        return $userReflection;
    }
}
