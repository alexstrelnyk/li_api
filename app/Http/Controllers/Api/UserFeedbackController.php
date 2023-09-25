<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ActivityNotFoundException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\UserFeedback\CreateRequest;
use App\Manager\ActivityManager;
use App\Manager\UserFeedbackManager;
use App\Models\ContentItem;
use App\Models\User;
use App\Models\UserFeedback;
use App\Services\Activity\Types\BookmarkedActivityInterface;
use App\Transformers\UserFeedback\UserFeedbackTransformer;
use Illuminate\Http\JsonResponse;
use Auth;

class UserFeedbackController extends ApiController
{
    /**
     *
     * @SWG\Post(
     *     path="/content-item-feedback",
     *     summary="Create new Feedback",
     *     tags={"Content Item Feedback"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Feedback Body",
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="content_item_id",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="reaction",
     *                 type="integer",
     *                 description="0 or 1"
     *             ),
     *             @SWG\Property(
     *                 property="response",
     *                 type="string"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
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
     * @param CreateRequest $request
     * @param UserFeedbackManager $userFeedbackManager
     * @param ActivityManager $activityManager
     * @return JsonResponse
     * @throws ActivityNotFoundException
     */
    public function create(
        CreateRequest $request,
        UserFeedbackManager $userFeedbackManager,
        ActivityManager $activityManager
    ): JsonResponse {
        /** @var User $user */
        $user = Auth::user();
        $contentItem = ContentItem::findOrFail($request->get('content_item_id'));
        $reaction = (bool) $request->get('reaction', false);
        $response = $request->get('response', null);

        $feedback = UserFeedback::firstOrCreate(['content_item_id' => $contentItem->id, 'user_id' => $user->id], ['reaction' => $reaction, 'response' => $response]);

        $userFeedbackManager->update($feedback, $reaction, $response);

        if ($feedback && Auth::user()) {
            $activityManager->createActivity(
                BookmarkedActivityInterface::TYPE,
                $user,
                [['class' => ContentItem::class, 'class_id' => $contentItem->id]]
            );
        }

        return new JsonResponse(fractal($feedback, new UserFeedbackTransformer()));
    }
}
