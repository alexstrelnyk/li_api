<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ScheduledEventException;
use App\Http\Requests\Api\ScheduleTopicOverview\CreateRequest;
use App\Manager\ScheduleTopicManager;
use App\Models\ScheduleTopic;
use App\Models\Topic;
use App\Models\User;
use App\Transformers\ScheduleTopic\ScheduleTopicTransformer;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Sentry;
use Swagger\Annotations as SWG;

class ScheduleTopicController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/schedule-topic",
     *     summary="Create new Schedule Topic, all properties in body required",
     *     tags={"Schedules Topic"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Schedules Topic Request body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/ScheduleTopicOverviewBody"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/ScheduleTopicOverview"
     *             )
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     )
     * );
     *
     * @param CreateRequest $request
     * @param ScheduleTopicManager $scheduleTopicManager
     *
     * @return JsonResponse
     */
    public function create(CreateRequest $request, ScheduleTopicManager $scheduleTopicManager): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var Topic $topic */
        $topic = Topic::findOrFail($request->get('topic_id'));

        if ($request->get('user_id')) {
            $user = User::findOrFail($request->get('user_id'));
        }

        /** @var Carbon $occursAt */
        $occursAt = Carbon::createFromFormat(CreateRequest::DATE_TIME_FORMAT, $request->get('occurs_at'));

        try {
            ScheduleTopic::ofUserAndTopic($user, $topic)->delete();
        } catch (\Exception $exception) {
            Sentry::captureException($exception);
        }

        try {
            $scheduleTopic = $scheduleTopicManager->createScheduleTopic($topic, $occursAt, $user);
        } catch (ScheduledEventException $exception) {
            return new JsonResponse(['error' => $exception]);
        }

        return new JsonResponse(fractal($scheduleTopic, new ScheduleTopicTransformer())->toArray());
    }
}
