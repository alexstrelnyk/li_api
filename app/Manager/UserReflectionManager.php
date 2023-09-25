<?php
declare(strict_types=1);

namespace App\Manager;

use App\Exceptions\ActivityNotFoundException;
use App\Factory\UserReflectionFactory;
use App\Models\ContentItem;
use App\Models\ContentItemUserProgress;
use App\Models\Program;
use App\Models\SilScore;
use App\Models\User;
use App\Models\UserReflection;
use App\Services\Activity\Types\WrittenReflectionActivityInterface;
use App\Services\SilScoreService\SilScoreService;
use App\Services\SilScoreService\Types\ReflectionCompletedSilScore;

class UserReflectionManager
{
    /**
     * @var UserReflectionFactory
     */
    private $userReflectionFactory;

    /**
     * @var SilScoreService
     */
    private $silScoreService;

    /**
     * @var ActivityManager
     */
    private $activityManager;

    /**
     * UserReflectionManager constructor.
     *
     * @param UserReflectionFactory $userReflectionFactory
     * @param SilScoreService $silScoreService
     * @param ActivityManager $activityManager
     */
    public function __construct(UserReflectionFactory $userReflectionFactory, SilScoreService $silScoreService, ActivityManager $activityManager)
    {
        $this->userReflectionFactory = $userReflectionFactory;
        $this->silScoreService = $silScoreService;
        $this->activityManager = $activityManager;
    }

    /**
     * @param UserReflection $userReflection
     */
    public function skip(UserReflection $userReflection): void
    {
        $userReflection->skipped = true;
        $userReflection->input = null;
    }

    /**
     * @param User $user
     * @param ContentItem $contentItem
     * @param Program $program
     * @param ContentItemUserProgress $userProgress
     *
     * @param bool $skipped
     * @param string $input
     *
     * @return UserReflection
     * @throws ActivityNotFoundException
     */
    public function createUserReflection(User $user, ContentItem $contentItem, Program $program, ContentItemUserProgress $userProgress, bool $skipped = false, string $input = ''): UserReflection
    {
        $oldUserReflections = UserReflection::ofUserAndContentItemAndProgram($user, $contentItem, $program)->withSilScores($user)->get();

        $userReflection = $this->userReflectionFactory->create($user, $contentItem, $program, $userProgress);
        $userReflection->input = $input;
        $userReflection->skipped = $skipped;

        $userReflection->save();

        $userProgress->reflection()->associate($userReflection);
        $userProgress->save();

        $this->activityManager->createActivity(
            WrittenReflectionActivityInterface::TYPE,
            $user,
            [['class' => ContentItem::class, 'class_id' => $contentItem->id]]
        );

        $oldUserReflections->each(function (UserReflection $reflection) {
            $reflection->silScores->each(function (SilScore $silScore) {
                $this->silScoreService->delete($silScore);
            });
        });

        if (!$userReflection->skipped) {
            $this->silScoreService->createPayloadedSilScore(new ReflectionCompletedSilScore($userReflection), $user);
        }

        return $userReflection;
    }

    /**
     * @param User $user
     * @param string $text
     *
     * @return UserReflection
     */
    public function crateOnboardingReflection(User $user, string $text): UserReflection
    {
        $userReflection = $this->userReflectionFactory->createOnboardingReflection($user);
        $userReflection->input = $text;
        $userReflection->skipped = false;
        $userReflection->save();

        return $userReflection;
    }
}
