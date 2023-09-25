<?php
declare(strict_types=1);

namespace App\Services\SilScoreService;

use App\Factory\SilScoreFactory;
use App\Manager\UserManager;
use App\Models\Interfaces\SilScoreInterface;
use App\Models\SilScore;
use App\Models\User;
use App\Notifications\SILScoreIsNotification;
use App\Services\ConsecutiveDaysService\ConsecutiveDaysService;
use App\Services\ConsecutiveDaysService\ConsecutiveDaysServiceInterface;
use App\Services\SilScoreService\Types\AccessContentAllFocusAreasSilScore;
use App\Services\SilScoreService\Types\Interfaces\ContentItemSilScore;
use App\Services\SilScoreService\Types\Interfaces\ReflectionSilScore;
use App\Services\SilScoreService\Types\OnboardingReflectionCompletedSilScore;
use App\Services\SilScoreService\Types\OnboardingSessionCompletedSilScore;
use App\Services\SilScoreService\Types\SessionCompletedSilScore;
use App\Services\SilScoreService\Types\EventScheduledSilScore;
use App\Services\SilScoreService\Types\FocusExploredSilScore;
use App\Services\SilScoreService\Types\ReflectionCompletedSilScore;
use Carbon\Carbon;
use DB;
use LogicException;

class SilScoreService
{
    /**
     * @var array
     */
    public $silScoreActivities = [
        SessionCompletedSilScore::TYPE => SilScoreInterface::SESSION_COMPLETED_SIL_SCORE,
        EventScheduledSilScore::TYPE => SilScoreInterface::EVENT_SCHEDULED_SIL_SCORE,
        FocusExploredSilScore::TYPE => SilScoreInterface::FOCUS_EXPLORED_SIL_SCORE,
        ReflectionCompletedSilScore::TYPE => SilScoreInterface::REFLECTION_COMPLETED_SIL_SCORE,
        AccessContentAllFocusAreasSilScore::TYPE => SilScoreInterface::ACCESS_CONTENT_ALL_FOCUS_AREAS_BONUS_SIL_SCORE
    ];

    /**
     * Number of consecutive days of app use for showing confetti
     */
    public const CONSECUTIVE_DAYS_OF_USE = 5;

    /**
     * @var UserManager
     */
    public $userManager;

    /**
     * @var ConsecutiveDaysService
     */
    public $consecutiveDaysService;

    /**
     * @var SilScoreFactory
     */
    private $silScoreFactory;

    /**
     * SilScoreActivityService constructor.
     *
     * @param SilScoreFactory $silScoreFactory
     * @param UserManager $userManager
     * @param ConsecutiveDaysServiceInterface $consecutiveDaysService
     */
    public function __construct(
        SilScoreFactory $silScoreFactory,
        UserManager $userManager,
        ConsecutiveDaysServiceInterface $consecutiveDaysService
    ) {
        $this->userManager = $userManager;
        $this->consecutiveDaysService = $consecutiveDaysService;
        $this->silScoreFactory = $silScoreFactory;
    }

    /**
     * @return array
     */
    public function getSilScoreTypes(): array
    {
        return [
            SessionCompletedSilScore::getType() => SessionCompletedSilScore::class,
            EventScheduledSilScore::getType() => EventScheduledSilScore::class,
            FocusExploredSilScore::getType() => FocusExploredSilScore::class,
            ReflectionCompletedSilScore::getType() => ReflectionCompletedSilScore::class,
            AccessContentAllFocusAreasSilScore::getType() => AccessContentAllFocusAreasSilScore::class,
            OnboardingReflectionCompletedSilScore::getType() => OnboardingReflectionCompletedSilScore::class,
            OnboardingSessionCompletedSilScore::getType() => OnboardingSessionCompletedSilScore::class
        ];
    }

    /**
     * @return array
     */
    public static function getAvailableSilScoreEvents(): array
    {
        return [
            SessionCompletedSilScore::TYPE,
            AccessContentAllFocusAreasSilScore::TYPE,
            EventScheduledSilScore::TYPE,
            FocusExploredSilScore::TYPE,
            ReflectionCompletedSilScore::TYPE
        ];
    }

    /**
     * @param string $silScoreActivityType
     * @param User $user
     * @param int $points
     * @return SilScore
     */
    public function createSilScore(string $silScoreActivityType, User $user, int $points): SilScore
    {
        $silScoreActivityModel = $this->silScoreFactory->create($user, $silScoreActivityType, $points);

        $silScoreActivityModel->save();

        $this->addPoints($user, $silScoreActivityModel->points);

        return $silScoreActivityModel;
    }

    /**
     * @param Types\SilScoreInterface $silScoreObject
     * @param User $user
     *
     * @return SilScore
     */
    public function createPayloadedSilScore(Types\SilScoreInterface $silScoreObject, User $user): SilScore
    {
        if ($silScoreObject instanceof ContentItemSilScore) {
            $silScore = $this->silScoreFactory->createForContentItem($user, $silScoreObject->getContentItem(), $silScoreObject::getType(), $silScoreObject->getPoints());
        } elseif ($silScoreObject instanceof ReflectionSilScore) {
            $silScore = $this->silScoreFactory->createForReflection($user, $silScoreObject->getReflection(), $silScoreObject::getType(), $silScoreObject->getPoints());
        } else {
            $silScore = $this->silScoreFactory->create($user, $silScoreObject::getType(), $silScoreObject->getPoints());
        }

        $silScore->save();

        $this->addPoints($user, $silScore->points);

        return $silScore;
    }

    /**
     * @param SilScore $silScore
     *
     * @throws
     */
    public function delete(SilScore $silScore): void
    {
        $silScoreType = $this->getByType($silScore->type);
        $user = $silScore->user;
        $this->removePoints($user, $silScoreType::getPoints());

        $silScore->delete();
    }

    /**
     * @param User $user
     * @return array
     */
    public function getSilScoreProgress(User $user): array
    {
        $silScoreBlock = SilScore::query()->where('user_id', $user->id)
            ->select([DB::raw('SUM(points) AS points'), DB::raw('date(created_at) AS created_at_date')])
            ->groupBy('created_at_date')
            ->get();

        $firstSilScore = $silScoreBlock->first();

        if ($firstSilScore instanceof SilScore) {

            $firstDate = Carbon::parse($silScoreBlock->first()->created_at_date);
            $lastDate = Carbon::parse($silScoreBlock->last()->created_at_date);
            $days = $firstDate->diffAsCarbonInterval($lastDate)->d;

            $silScoreBlock = $silScoreBlock->pluck('points', 'created_at_date')->toArray();

            $silScoreProgress = array_fill(1, $days, 0);

            foreach ($silScoreProgress as $key => $item) {
                $pastDate = Carbon::now()->subDays($days - $key)->toDateString();

                if (array_key_exists($pastDate, $silScoreBlock)) {
                    $val = $silScoreBlock[$pastDate];
                    $silScoreProgress[$key] = ($key === 1) ? $val : $val + $silScoreProgress[$key - 1];
                } else {
                    $silScoreProgress[$key] = ($key === 1) ? $item : $item + $silScoreProgress[$key - 1];
                }
            }

            return array_values($silScoreProgress);
        }

        return [0];
    }

    /**
     * @param User $user
     * @param int $points
     */
    public function addPoints(User $user, int $points): void
    {
        $notificationPoints = [
            100,
            150,
            250,
            300
        ];

        $p = 0;

        $newSilScore = $user->sil_score + $points;

        foreach ($notificationPoints as $notPoints) {
            if ($user->sil_score < $notPoints && $newSilScore >= $notPoints) {
                $p = $notPoints;
                break;
            }
        }

        $user->sil_score = $newSilScore;
        $user->update();

        if ($p !== 0) {
            $user->notify(new SILScoreIsNotification($p));
        }
    }

    /**
     * @param User $user
     * @param int $points
     */
    public function removePoints(User $user, int $points): void
    {
        $user->sil_score -= $points;
        $user->save();
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function checkConsecutiveDays(User $user): int
    {
        $consecutiveDays = $this->consecutiveDaysService->getConsecutiveDays($user);

        if ($consecutiveDays === self::CONSECUTIVE_DAYS_OF_USE) {
            $this->addPoints($user, SilScoreInterface::CONSECUTIVE_DAYS_Of_USE_BONUS_SIL_SCORE);

            return SilScoreInterface::CONSECUTIVE_DAYS_Of_USE_BONUS_SIL_SCORE;
        }

        return 0;
    }

    /**
     * @param User $user
     * @return int
     */
    public function checkDiffs(User $user): int
    {
        $now = Carbon::now();

        return $user->getLastSeen()->startOfDay()->diffInDays($now->startOfDay());
    }

    /**
     * @param string $type
     *
     * @return Types\SilScoreInterface
     */
    private function getByType(string $type)
    {
        if (array_key_exists($type, $this->getSilScoreTypes())) {
            return $this->getSilScoreTypes()[$type];
        }

        throw new LogicException(sprintf('Could not resolve sil score type [%s]', $type));
    }
}
