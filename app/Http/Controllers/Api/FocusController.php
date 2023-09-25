<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Focus\CreateRequest;
use App\Http\Requests\Api\Focus\UpdatePatchRequest;
use App\Http\Requests\Api\Focus\UpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponseInterface;
use App\Mangers\FocusManager;
use App\Models\Client;
use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\FocusArea;
use App\Models\Program;
use App\Models\Topic;
use App\Models\User;
use App\Services\Api\FocusService;
use App\Transformers\ContentItem\ContentItemTransformerInterface;
use App\Transformers\Focus\FocusTransformerInterface;
use App\Transformers\Topic\TopicTransformerInterface;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Swagger\Annotations as SWG;
use Auth;

class FocusController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/focuses",
     *     summary="Get list of focuses",
     *     tags={"Focuses"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="sort",
     *         description="Direction of sorting (asc, desc) Default: asc",
     *         in="query",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         description="Count of records per page. Default: 10",
     *         in="query",
     *         type="integer",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         description="Offset of records. Default: 0",
     *         in="query",
     *         type="integer",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         description="Status. (Draft - 1, Review - 2, Published - 3)",
     *         in="query",
     *         type="integer",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="List of focuses",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/Focus")
     *                     )
     *                 )
     *             },
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param FocusTransformerInterface $transformer
     * @param ListResponseInterface $listResponse
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ListRequest $request, FocusTransformerInterface $transformer, ListResponseInterface $listResponse): JsonResponse
    {
        $this->authorize('list', Focus::class);

        /** @var User $user */
        $user = Auth::user();

        $builder = Focus::ofUser($user);

        if ($user->permission === User::APP_USER) {
            $client = $user->client;

            $program = $client->activeProgram;

            $programId = $program instanceof Program ? $program->id : null;

            $builder->published()->whereHas('focusAreas', static function (Builder $builder) use ($programId) {
                /** @var FocusArea $builder */
                $builder->published()->where('program_id', $programId ?? null);
            })->orderBy('updated_at', 'desc');
        }

        return $listResponse
            ->setBuilder($builder)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
//            ->setSortField('status')
//            ->setSortDirection($request->getSortDirection())
            ->setTransformer($transformer)
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/focuses",
     *     summary="Create new Focus",
     *     tags={"Focuses"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Focus Body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/FocusBody"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="focus",
     *                 type="array",
     *                 @SWG\Items(
     *                     ref="#/definitions/Focus"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     * @param CreateRequest $request
     * @param FocusService $focusService
     *
     * @param FocusTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function create(CreateRequest $request, FocusService $focusService, FocusTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('create', Focus::class);

        $focus = $focusService->create($request->validated());

        return new JsonResponse(fractal($focus, $transformer)->toArray());
    }

    /**
     * @SWG\Get(
     *     path="/focuses/{id}",
     *     summary="Get focus data",
     *     tags={"Focuses"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Focus id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success operation",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="focus",
     *                 type="array",
     *                 @SWG\Items(
     *                     ref="#/definitions/Focus"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     * )
     *
     * @param Focus $focus
     * @param FocusTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Focus $focus, FocusTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('view', $focus);

        return new JsonResponse(fractal($focus, $transformer)->toArray());
    }

    /**
     * @SWG\Put(
     *     path="/focuses/{id}",
     *     summary="Update focus. All properties is required.",
     *     tags={"Focuses"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Focus id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         description="Focus body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/FocusBody"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Focus was successfully updated",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(
     *                     ref="#/definitions/Focus"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param Focus $focus
     * @param UpdateRequest $request
     * @param FocusService $focusService
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Focus $focus, UpdateRequest $request, FocusService $focusService): JsonResponse
    {
        $this->authorize('update', $focus);

        $response = $focusService->update($focus, $request->validated());

        return response()->json($response->getData(), $response->getStatus());
    }

    /**
     * @SWG\Patch(
     *     path="/focuses/{id}",
     *     summary="Update focus properties",
     *     tags={"Focuses"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Focus id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         description="Focus body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/FocusBody"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Focus was successfully updated",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(
     *                     ref="#/definitions/Focus"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param Focus $focus
     * @param UpdatePatchRequest $request
     * @param FocusService $focusService
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function updatePatch(Focus $focus, UpdatePatchRequest $request, FocusService $focusService): JsonResponse
    {
        $this->authorize('update', $focus);

        $response = $focusService->updatePatch($focus, $request->validated());

        return response()->json($response->getData(), $response->getStatus());
    }

    /**
     * @SWG\Delete(
     *     path="/focuses/{id}",
     *     summary="Delete focus",
     *     tags={"Focuses"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         description="Focus id",
     *         in="path",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Focus was successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     * )
     *
     * @param Focus $focus
     * @param FocusService $focusService
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     * @throws Exception
     */
    public function delete(Focus $focus, FocusService $focusService): JsonResponse
    {
        $this->authorize('delete', $focus);

        $response = $focusService->delete($focus);

        return response()->json($response->getData(), $response->getStatus());
    }

    /**
     * @SWG\Get(
     *     path="/focuses/{id}/topics",
     *     summary="Get list of topics",
     *     tags={"Focuses"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         description="Filter by focus id",
     *         in="path",
     *         type="string",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         description="Offset",
     *         in="query",
     *         type="string",
     *         required=false
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         description="Limit",
     *         in="query",
     *         type="string",
     *         required=false
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="limit",
     *                 type="integer",
     *                 default=10
     *             ),
     *             @SWG\Property(
     *                 property="offset",
     *                 type="integer",
     *                 default=0
     *             ),
     *             @SWG\Property(
     *                 property="focus",
     *                 ref="#/definitions/Focus",
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(ref="#/definitions/TopicOfUser"),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param Focus $focus
     * @param FocusTransformerInterface $focusTransformer
     * @param TopicTransformerInterface $topicTransformer
     * @param ListResponseInterface $listResponse
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function topics(
        ListRequest $request,
        Focus $focus,
        FocusTransformerInterface $focusTransformer,
        TopicTransformerInterface $topicTransformer,
        ListResponseInterface $listResponse
    ): JsonResponse {
        $this->authorize('view', $focus);
        $this->authorize('list', Topic::class);

        /** @var User $user */
        $user = Auth::user();

        if ($user->permission === User::APP_USER) {
            $client = $user->client;

            if ($client instanceof Client) {
                $program = $client->activeProgram;

                if ($program instanceof Program) {
                    $focusArea = FocusArea::query()->where('program_id', $program->id)
                        ->where('focus_id', $focus->id)
                        ->first();

                    $ids = $focusArea->topics->pluck('id')->toArray();

                    $builder = Topic::published()->whereIn('id', $ids);
                } else {
                    $builder = Topic::whereNull('id');
                }
            } else {
                $builder = Topic::whereNull('id');
            }
        } else {
            $builder = Topic::byFocus($focus)->ofUser($user);
        }

        return $listResponse
            ->setBuilder($builder)
            ->setLimit($request->getLimit())
            ->setOffset($request->getOffset())
            ->setTransformer($topicTransformer)
            ->addValue('focus', fractal($focus, $focusTransformer)->toArray()['data'])
            ->getResponse();
    }

    /**
     * @SWG\Get(
     *     path="/focuses/{id}/content-items",
     *     summary="Get list of content items",
     *     tags={"Focuses"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         description="Filter by Focus ID",
     *         in="path",
     *         type="string",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         description="Offset",
     *         in="query",
     *         type="string",
     *         required=false
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         description="Limit",
     *         in="query",
     *         type="string",
     *         required=false
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="limit",
     *                 type="integer",
     *                 default=10
     *             ),
     *             @SWG\Property(
     *                 property="offset",
     *                 type="integer",
     *                 default=0
     *             ),
     *             @SWG\Property(
     *                 property="focus",
     *                 ref="#/definitions/Focus",
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(ref="#/definitions/TopicOfUser"),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param ListResponseInterface $response
     * @param Focus $focus
     * @param FocusTransformerInterface $focusTransformer
     * @param ContentItemTransformerInterface $contentItemTransformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function contentItems(
        ListRequest $request,
        ListResponseInterface $response,
        Focus $focus,
        FocusTransformerInterface $focusTransformer,
        ContentItemTransformerInterface $contentItemTransformer
    ): JsonResponse {
        $this->authorize('view', $focus);
        $this->authorize('list', ContentItem::class);
        $builder = $focus->contentItems()->getQuery();

        return $response
            ->setBuilder($builder)
            ->setTransformer($contentItemTransformer)
            ->addValueWithTransformer('focus', $focus, $focusTransformer)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/focuses/{id}/reset",
     *     summary="Reset progress of Focus",
     *     security={{"apiToken": {}}},
     *     tags={"Focuses"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="Focus ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Focus data",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     * )
     *
     * @param Focus $focus
     * @param FocusManager $focusManager
     * @param FocusTransformerInterface $transformer
     *
     * @return JsonResponse
     */
    public function reset(Focus $focus, FocusManager $focusManager, FocusTransformerInterface $transformer): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $focusManager->resetProgress($focus, $user);

        return new JsonResponse(fractal($focus, $transformer)->toArray());
    }
}
