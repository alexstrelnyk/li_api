<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Topic\UpdatePatchRequest;
use App\Http\Requests\Api\Topic\CreateRequest;
use App\Http\Requests\Api\Topic\OrderRequest;
use App\Http\Requests\Api\Topic\UpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponseInterface;
use App\Manager\ContentItemManager;
use App\Manager\TopicManager;
use App\Models\Client;
use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\FocusAreaTopic;
use App\Models\Topic;
use App\Models\User;
use App\Transformers\ContentItem\ContentItemTransformerInterface;
use App\Transformers\Topic\TopicTransformerInterface;
use Auth;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Swagger\Annotations as SWG;

class TopicController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/topics",
     *     summary="Get list of topics",
     *     tags={"Topics"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="limit",
     *         description="Limit (Defaul: 10)",
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
     *     @SWG\Parameter(
     *         name="sort",
     *         description="Sort direction",
     *         in="query",
     *         type="string",
     *         required=false
     *     ),
     *     @SWG\Parameter(
     *         name="focus_id",
     *         description="Filter by focus id",
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
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(ref="#/definitions/Topic"),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     *
     * @param ListRequest $request
     * @param TopicTransformerInterface $transformer
     *
     * @param ListResponseInterface $listResponse
     *
     * @param TopicManager $topicManager
     *
     * @return JsonResponse
     */
    public function index(ListRequest $request, TopicTransformerInterface $transformer, ListResponseInterface $listResponse, TopicManager $topicManager): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->permission === User::APP_USER && $request->get('focus_id')) {
            $focus = Focus::findOrFail($request->get('focus_id'));
            $builder = Topic::ofAppUser($focus, $user);
        } else {
            $builder = Topic::ofUser($user);
        }

        return $listResponse
            ->setBuilder($builder)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->setTransformer($transformer)
            ->setResultHandler(static function (Collection $collection) use ($topicManager) {
                $client = Auth::user()->client;
                if ($client instanceof Client) {
                    $sorting = $topicManager->getSorting($client);
                    if (!$sorting->count()) {
                        $sorting = $topicManager->createSorting($client);
                    }

                    $sorting = $sorting->keyBy('topic_id')->toArray();

                    $iterator = 0;

                    $result = $collection->sortBy(static function (Topic $topic) use ($sorting, &$iterator) {
                        $iterator++;
                        return $sorting[$topic->id]->order ?? $iterator;
                    });

                    return $result;
                }

                return $collection;
            })->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/topics",
     *     summary="Create new Topic, all properties in body required",
     *     tags={"Topics"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Topic body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/TopicBodyData"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/Topic"
     *             ),
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
     *     ),
     * )
     *
     * @param CreateRequest $request
     * @param TopicManager $topicManager
     * @param ContentItemManager $contentItemManager
     * @param TopicTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function create(CreateRequest $request, TopicManager $topicManager, ContentItemManager $contentItemManager, TopicTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('create', Topic::class);

        $focus = Focus::findOrFail($request->get('focus_id'));

        if ($request->getHasPractice()) {
            $contentItem = ContentItem::findOrFail($request->getContentItemId());
            $contentItemManager->setIsPractice($contentItem, true);
            $topic = $topicManager->createPracticeBased($focus, $contentItem, $request->except('focus_id'));
        } else {
            $topic = $topicManager->createLearnBased($focus, $request->except('focus_id'));
        }

        return new JsonResponse(fractal($topic, $transformer)->toArray(), 201);
    }

    /**
     *
     * @SWG\Get(
     *     path="/topics/{id}",
     *     description="Get topic data",
     *     tags={"Topics"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Topic id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success operation",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(
     *                     ref="#/definitions/Topic"
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Topic not found",
     *     ),
     * )
     *
     * @param Topic $topic
     * @param TopicTransformerInterface $transformer
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Topic $topic, TopicTransformerInterface $transformer): JsonResponse
    {
//        $this->authorize('view', $topic);

        return new JsonResponse(fractal($topic, $transformer)->toArray());
    }

    /**
     * @SWG\Put(
     *     path="/topics/{id}",
     *     summary="Update topic, all properties in body required",
     *     tags={"Topics"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Topic id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         description="Topic body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/TopicBodyData"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Topic was successfully updated",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(
     *                     ref="#/definitions/Topic"
     *                 ),
     *             ),
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
     *     ),
     * )
     *
     * @param Topic $topic
     * @param UpdateRequest $request
     * @param TopicManager $topicManager
     * @param TopicTransformerInterface $transformer
     *
     * @return JsonResponse
     */
    public function update(Topic $topic, UpdateRequest $request, TopicManager $topicManager, TopicTransformerInterface $transformer): JsonResponse
    {
        $topicManager->update($topic, $request->validated());

        return new JsonResponse(fractal($topic, $transformer)->toArray());
    }

    /**
     * @SWG\Patch(
     *     path="/topics/{id}",
     *     summary="Update topic properties",
     *     tags={"Topics"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Topic id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         description="Topic body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/TopicBodyData"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Topic was successfully updated",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(
     *                     ref="#/definitions/Topic"
     *                 ),
     *             ),
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
     *     ),
     * )
     *
     * @param Topic $topic
     * @param UpdatePatchRequest $request
     * @param TopicManager $topicManager
     * @param TopicTransformerInterface $transformer
     *
     * @return JsonResponse
     */
    public function updatePatch(Topic $topic, UpdatePatchRequest $request, TopicManager $topicManager, TopicTransformerInterface $transformer): JsonResponse
    {
        $topicManager->update($topic, $request->validated());

        return new JsonResponse(fractal($topic, $transformer)->toArray());
    }

    /**
     * @SWG\Post(
     *     path="/topics/order",
     *     summary="Set custom topic order",
     *     security={{"apiToken": {}}},
     *     tags={"Topics"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="order",
     *                 type="array",
     *                 @SWG\Items(
     *                     @SWG\Property(
     *                         property="topicId",
     *                         type="integer"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Topic was successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     * @param $request
     * @param TopicManager $topicManager
     *
     * @return JsonResponse
     */
    public function order(OrderRequest $request, TopicManager $topicManager): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $client = $user->client;
        $topicManager->setNewOrder($client, $request->get('order'));

        return new JsonResponse();
    }

    /**
     * @SWG\Delete(
     *     path="/topics/{id}",
     *     summary="Delete topic",
     *     tags={"Topics"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Topic id",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Topic was successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param Topic $topic
     * @param TopicManager $topicManager
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Topic $topic, TopicManager $topicManager): JsonResponse
    {
        $result = $topicManager->delete($topic);

        return new JsonResponse(['message' => $result ? 'Topic was successful deleted' : 'Something went wrong'], 204);
    }

    /**
     * @SWG\Get(
     *     path="/topics/{id}/content-items",
     *     summary="Get list of content items",
     *     tags={"Topics"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="sort",
     *         description="Sort direction",
     *         in="query",
     *         type="string",
     *         required=false
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         description="Topic ID",
     *         in="path",
     *         type="integer",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="topic",
     *                         ref="#/definitions/TopicOfUser"
     *                     )
     *                 ),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Property(
     *                             property="completed",
     *                             @SWG\Items(ref="#/definitions/ContentItem")
     *                         ),
     *                         @SWG\Property(
     *                             property="in_progress",
     *                             @SWG\Items(ref="#/definitions/ContentItem")
     *                         )
     *                     )
     *                 )
     *             },
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param Topic $topic
     * @param TopicTransformerInterface $topicTransformer
     * @param ContentItemTransformerInterface $contentItemTransformer
     *
     * @return JsonResponse
     */
    public function contentItems(
        Topic $topic,
        TopicTransformerInterface $topicTransformer,
        ContentItemTransformerInterface $contentItemTransformer
    ): JsonResponse {
        /** @var User $user */
        $user = Auth::user();
        $client = $user->client;

        $program = $client->activeProgram;

        $topicArea = FocusAreaTopic::where('topic_id', $topic->id)->whereHas('focusArea', static function (Builder $builder) use ($program) {
            $builder->where('program_id', $program->id);
        })->first();

        if ($topicArea instanceof FocusAreaTopic) {
            $contentItemsInProgress = $topicArea->contentItems()->inProgress($user)->get();
            $contentItemsCompleted = $topicArea->contentItems()->completed($user)->get();
        } else {
            $contentItemsInProgress = [];
            $contentItemsCompleted = [];
        }

        return new JsonResponse([
            'topic' => fractal($topic, $topicTransformer)->toArray()['data'],
            'data' => [
                'completed' => fractal()->collection($contentItemsCompleted)->transformWith($contentItemTransformer)->toArray()['data'],
                'in_progress' => fractal()->collection($contentItemsInProgress)->transformWith($contentItemTransformer)->toArray()['data'],
            ]
        ]);

    }
}
