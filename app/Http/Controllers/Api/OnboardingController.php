<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OnBoarding\CompleteRequest;
use App\Manager\OnBoardingManager;
use App\Manager\UserReflectionManager;
use App\Models\SilScore;
use App\Models\User;
use App\Services\SilScoreService\SilScoreService;
use App\Services\SilScoreService\Types\OnboardingReflectionCompletedSilScore;
use App\Services\SilScoreService\Types\OnboardingSessionCompletedSilScore;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Swagger\Annotations as SWG;

class OnboardingController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/onboarding/complete",
     *     summary="Complete On Boarding session and get SIL score",
     *     security={{"apiToken": {}}},
     *     tags={"Onboarding"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             required={"reflection"},
     *             @SWG\Property(
     *                 property="reflection",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Success",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="sil_score",
     *                         type="integer"
     *                     ),
     *                     @SWG\Property(
     *                         property="bonus_points",
     *                         type="integer"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @param CompleteRequest $request
     * @param OnBoardingManager $boardingManager
     *
     * @param SilScoreService $silScoreService
     * @param UserReflectionManager $userReflectionManager
     *
     * @return JsonResponse
     */
    public function complete(CompleteRequest $request, OnBoardingManager $boardingManager, SilScoreService $silScoreService, UserReflectionManager $userReflectionManager): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $silScores = new Collection();

        $boardingManager->complete($user);

        if ($request->getReflection() !== null) {
            $userReflectionManager->crateOnboardingReflection($user, $request->getReflection());
            $onBoardingReflectionCompletedSilScore = $silScoreService->createPayloadedSilScore(new OnboardingReflectionCompletedSilScore(), $user);
            $silScores->add($onBoardingReflectionCompletedSilScore);
        }

        $userSilScores = $user->sil_score;

        $onBoardingSessionCompletedSilScore = SilScore::ofType(OnboardingSessionCompletedSilScore::getType())->first();

        if ($onBoardingSessionCompletedSilScore instanceof SilScore) {
            $userSilScores -= OnboardingSessionCompletedSilScore::getPoints();
        } else {
            $onBoardingSessionCompletedSilScore = $silScoreService->createPayloadedSilScore(new OnboardingSessionCompletedSilScore(), $user);
        }

        $silScores->add($onBoardingSessionCompletedSilScore);

        return new JsonResponse([
            'data' => [
                'sil_score' => $userSilScores,
                'bonus_points' => $silScores->sum('points')
            ]
        ], 200);
    }
}
