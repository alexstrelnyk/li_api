<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Activity\CreateActivityRequest;
use App\Models\User;
use App\Services\SilScoreService\SilScoreService;
use App\Transformers\SilScore\SilScoreTransformerInterface;
use Illuminate\Http\JsonResponse;
use Auth;

class SilScoreController extends ApiController
{
    /**
     * @SWG\Post(
     *     path="/sil-scores",
     *     summary="Add new event for calculating sil score",
     *     security={{"apiToken": {}}},
     *     tags={"Sil Score"},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Event Body",
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="event_name",
     *                 type="string"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="sil_score",
     *                  type="integer",
     *              ),
     *              @SWG\Property(
     *                  property="bonus_points",
     *                  type="integer",
     *              ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param CreateActivityRequest $request
     * @param SilScoreService $silScoreActivityService
     * @param SilScoreTransformerInterface $silScoreTransformer
     *
     * @return JsonResponse
     */
    public function addEvent(
        CreateActivityRequest $request,
        SilScoreService $silScoreActivityService,
        SilScoreTransformerInterface $silScoreTransformer
    ): JsonResponse {
        /** @var User $user */
        $user = Auth::user();
        $data = $request->validated();

        $silScore = $silScoreActivityService->createSilScore($data['event_name'], $user, $silScoreActivityService->silScoreActivities[$data['event_name']]);

        return new JsonResponse(fractal($silScore, $silScoreTransformer)->toArray());
    }
}
