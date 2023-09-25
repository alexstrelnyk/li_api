<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponseInterface;
use App\Manager\ActivityManager;
use App\Models\Activity;
use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\User;
use App\Models\UserReflection;
use App\Transformers\Activity\ActivityTransformerInterface;
use App\Transformers\JournalActivity\JournalActivityTransformerInterface;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Auth;
use Swagger\Annotations as SWG;

class ActivityController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/activity/me",
     *     summary="Get Me",
     *     tags={"Activity"},
     *     operationId="getMe",
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="offset",
     *         type="integer",
     *         description="Offset",
     *         in="query"
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         type="integer",
     *         description="Limit",
     *         in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/Activity")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param ActivityTransformerInterface $activityTransformer
     * @param ListResponseInterface $listResponse
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function me(
        ListRequest $request,
        ActivityTransformerInterface $activityTransformer,
        ListResponseInterface $listResponse
    ): JsonResponse {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();
        $builder = Activity::ofUser($user)->orderBy('created_at', 'desc');

        return $listResponse
            ->setBuilder($builder)
            ->setTransformer($activityTransformer)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->getResponse();
    }

    /**
     * @SWG\Get(
     *     path="/activity/company",
     *     summary="Get Company",
     *     tags={"Activity"},
     *     operationId="getCompany",
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="offset",
     *         type="integer",
     *         description="Offset",
     *         in="query"
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         type="integer",
     *         description="Limit",
     *         in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/Activity")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param ActivityTransformerInterface $activityTransformer
     * @param ListResponseInterface $listResponse
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function company(
        ListRequest $request,
        ActivityTransformerInterface $activityTransformer,
        ListResponseInterface $listResponse
    ): JsonResponse {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();

        $userIds = User::query()->getByActivitySharing()->where('client_id', $user->client->id)->pluck('id')->toArray();
        $builder = Activity::query()->whereIn('user_id', $userIds)->orderBy('created_at', 'desc');

        return $listResponse
            ->setBuilder($builder)
            ->setTransformer($activityTransformer)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->getResponse();
    }

    /**
     * @SWG\Get(
     *     path="/activity/journal",
     *     summary="Get Journal",
     *     tags={"Activity"},
     *     operationId="getJournal",
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="focus_id",
     *         description="Filter by Focus ID",
     *         type="integer",
     *         in="query"
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         description="Limit (Default: 10)",
     *         in="query",
     *         type="integer",
     *         required=false
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         description="Offset (Default: 0)",
     *         in="query",
     *         type="integer",
     *         required=false
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Property(
     *                             property="color",
     *                             type="string"
     *                         ),
     *                         @SWG\Property(
     *                             property="title",
     *                             type="string"
     *                         ),
     *                         @SWG\Property(
     *                             property="description",
     *                             type="string"
     *                          ),
     *                         @SWG\Property(
     *                             property="date",
     *                             type="string"
     *                          ),
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param JournalActivityTransformerInterface $journalActivityTransformer
     * @param ListResponseInterface $listResponse
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function journal(
        ListRequest $request,
        JournalActivityTransformerInterface $journalActivityTransformer,
        ListResponseInterface $listResponse
    ): JsonResponse {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();
        $reflections = UserReflection::byUser($user)->notSkipped()->orderBy('created_at', 'desc');

        $focuses = Focus::whereHas('contentItems', static function (Builder $builder) use ($user) {
            $builder->whereHas('userReflections', static function (Builder $builder) use ($user) {
                /** @var UserReflection $builder */
                $builder->byUser($user)->notSkipped();
            });
        })->select('id', 'title')->get()->toArray();

        if ($request->get('focus_id')) {
            $focus = Focus::findOrFail($request->get('focus_id'));

            $reflections = $reflections->whereHas('contentItem', static function (Builder $builder) use ($focus) {
                /** @var ContentItem $builder */
                $builder->ofFocus($focus);
            });
        }

        return $listResponse
            ->setBuilder($reflections)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->setTransformer($journalActivityTransformer)
            ->addValue('focuses', $focuses)
            ->getResponse();
    }

    /**
     * @SWG\Get(
     *     path="/activity/status",
     *     summary="Get status of new activities",
     *     tags={"Activity"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="limit",
     *         description="Limit (Default: 10)",
     *         in="query",
     *         type="integer",
     *         required=false
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         description="Offset (Default: 0)",
     *         in="query",
     *         type="integer",
     *         required=false
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                 property="data",
     *                 @SWG\Property(
     *                     property="has_new_activities",
     *                     type="boolean"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function status():JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $count = Activity::notViewed($user)->count();

        return new JsonResponse(['has_new_activities' => $count > 0]);
    }

    /**
     * @SWG\Post(
     *     path="/activity/viewed",
     *     summary="Set status of viewed activity",
     *     tags={"Activity"},
     *     security={{"apiToken": {}}},
     *     @SWG\Response(
     *         response="204",
     *         description="No Content",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param ActivityManager $activityManager
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function viewActivities(ActivityManager $activityManager): JsonResponse
    {
        $this->authorize('appAuth', User::class);
        /** @var Activity[] $activities */
        /** @var User $user */
        $user = Auth::user();
        $activities = Activity::notViewed($user)->get();
        $activityManager->viewActivities($activities, $user);
        return new JsonResponse(null, 204);
    }
}
