<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ActivityNotFoundException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\ContentItem\CreateRequest;
use App\Http\Requests\Api\ContentItem\DislikeRequest;
use App\Http\Requests\Api\ContentItem\UpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponseInterface;
use App\Manager\ContentItemManager;
use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\Interfaces\ContentItemTypeInterface;
use App\Models\SilScore;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserReflection;
use App\Services\SilScoreService\SilScoreService;
use App\Services\SilScoreService\Types\SessionCompletedSilScore;
use App\Transformers\ContentItem\ContentItemTransformerInterface;
use App\Transformers\UserFeedback\UserFeedbackTransformerInterface;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Swagger\Annotations as SWG;
use Auth;

class ContentItemController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/content-items",
     *     summary="Get list of Content Items",
     *     tags={"Content Items"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="created_by",
     *         description="Id of Author",
     *         in="query",
     *         type="integer",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="topic_id",
     *         description="Id of Topic",
     *         in="query",
     *         type="integer",
     *         required=false,
     *     ),
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
     *         description="List of Content Items",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/ContentItem")
     *                     )
     *                 )
     *             }
     *         )
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
     * @param ContentItemTransformerInterface $contentItemTransformer
     * @param ListResponseInterface $response
     * @return JsonResponse
     */
    public function index(ListRequest $request, ContentItemTransformerInterface $contentItemTransformer, ListResponseInterface $response): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $builder = ContentItem::ofUser($user);

        if ($user->permission === User::LI_ADMIN || $user->permission === User::LI_CONTENT_EDITOR) {
            $builder->where('is_practice', false);
        }

        return $response
            ->setBuilder($builder)
            ->setTransformer($contentItemTransformer)
            ->setWhere([
                'topic_id' => $request->get('topic_id'),
                'created_by' => $request->get('created_by')
            ])->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->setSortField(Auth::user()->permission === User::LI_ADMIN ? 'created_at' : 'id')
            ->setSortDirection($request->get('sort', Auth::user()->permission === User::LI_ADMIN ? 'desc' : 'asc'))
            ->getResponse();
    }

    /**
     * @SWG\Get(
     *     path="/content-items/{id}",
     *     summary="Get Content Item data",
     *     tags={"Content Items"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         description="Id of Content Item",
     *         in="path",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Content Item data",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ContentItem"
     *              )
     *         )
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
     * @param ContentItem $contentItem
     * @param ContentItemTransformerInterface $contentItemTransformer
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ContentItem $contentItem, ContentItemTransformerInterface $contentItemTransformer): JsonResponse
    {
//        $this->authorize('view', $contentItem);

        return new JsonResponse(fractal($contentItem, $contentItemTransformer)->toArray());
    }

    /**
     *
     * @SWG\Post(
     *     path="/content-items",
     *     summary="Create Content Item",
     *     tags={"Content Items"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Id of Content Item",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="content_type",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="focus_id",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="topic_id",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="primer_title",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="primer_content",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="reading_time",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="info_content_image",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_quick_tip",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_full_content",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_video_uri",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_audio_uri",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_source_title",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_source_link",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="has_reflection",
     *                 type="boolean",
     *             ),
     *             @SWG\Property(
     *                 property="reflection_help_text",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="status",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="title",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="primer_notification_text",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="content_notification_text",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="reflection_notification_text",
     *                 type="string",
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Content Item data",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ContentItem"
     *              )
     *         )
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
     * @param CreateRequest $request
     * @param ContentItemTransformerInterface $contentItemTransformer
     * @param ContentItemManager $contentItemManager
     * @return JsonResponse
     */
    public function create(
        CreateRequest $request,
        ContentItemTransformerInterface $contentItemTransformer,
        ContentItemManager $contentItemManager
    ): JsonResponse {
        // $this->authorize('create', ContentItem::class);

        $focus = Focus::findOrFail($request->getFocusId());

        try {
            $contentItem = $contentItemManager->create($focus, $request->validated());
        } catch (Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()]);
        }

        return new JsonResponse(fractal($contentItem, $contentItemTransformer)->toArray());
    }

    /**
     * @SWG\Put(
     *     path="/content-items/{id}",
     *     summary="Update Content Item",
     *     tags={"Content Items"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="ContentItem Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         description="Id of Content Item",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="content_type",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="topic_id",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="primer_title",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="primer_content",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="reading_time",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="info_content_image",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_quick_tip",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_full_content",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_video_uri",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_source_title",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="info_source_link",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="has_reflection",
     *                 type="boolean",
     *             ),
     *             @SWG\Property(
     *                 property="reflection_help_text",
     *                 type="boolean",
     *             ),
     *             @SWG\Property(
     *                 property="status",
     *                 type="integer",
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Content Item data",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ContentItem"
     *              )
     *         )
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
     * @param ContentItem $contentItem
     * @param UpdateRequest $request
     * @param ContentItemManager $contentItemManager
     * @param ContentItemTransformerInterface $contentItemTransformer
     * @return JsonResponse
     */
    public function update(
        ContentItem $contentItem,
        UpdateRequest $request,
        ContentItemManager $contentItemManager,
        ContentItemTransformerInterface $contentItemTransformer
    ): JsonResponse {
        $contentItemManager->update($contentItem, $request->validated());

        return new JsonResponse(fractal($contentItem, $contentItemTransformer)->toArray());
    }

    /**
     * @SWG\Post(
     *     path="/content-items/{id}/reflected",
     *     summary="Commit reflection viewed",
     *     security={{"apiToken": {}}},
     *     tags={"Content Items"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="Content Item ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="No content"
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
     * @param ContentItem $contentItem
     * @param ContentItemManager $contentItemManager
     *
     * @return JsonResponse
     */
    public function reflected(ContentItem $contentItem, ContentItemManager $contentItemManager): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $contentItemManager->setViewed($contentItem, $user, $contentItemManager::REFLECTION_VIEWED_TYPE);

        return new JsonResponse(null, 204);
    }

    /**
     * @SWG\Post(
     *     path="/content-items/{id}/start",
     *     summary="Commit started content item",
     *     security={{"apiToken": {}}},
     *     tags={"Content Items"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="Content Item ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="No content",
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
     * @param ContentItem $contentItem
     * @param ContentItemManager $contentItemManager
     *
     * @return JsonResponse
     * @throws ActivityNotFoundException
     */
    public function start(ContentItem $contentItem, ContentItemManager $contentItemManager): JsonResponse
    {
        if ($contentItem->content_type === ContentItemTypeInterface::TIP_CONTENT_TYPE) {
            return new JsonResponse(['error' => 'You can\'t start Content Item of Tip type']);
        }

        if (!$contentItem->getTopic() instanceof Topic) {
            return new JsonResponse(['error' => 'Content item has no related topic!']);
        }

        /** @var User $user */
        $user = Auth::user();
        $contentItemManager->start($contentItem, $user);

        return new JsonResponse(null, 204);
    }

    /**
     *
     * @SWG\Post(
     *     path="/content-items/{id}/watched",
     *     summary="Commit watched content item",
     *     security={{"apiToken": {}}},
     *     tags={"Content Items"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="Content Item ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="No content",
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
     * @param ContentItem $contentItem
     * @param ContentItemManager $contentItemManager
     *
     * @return JsonResponse
     * @throws ActivityNotFoundException
     */
    public function watched(ContentItem $contentItem, ContentItemManager $contentItemManager): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $contentItemManager->watched($contentItem, $user);

        return new JsonResponse(null, 204);
    }

    /**
     * @SWG\Post(
     *     path="/content-items/{id}/reset",
     *     summary="Reset Conten Items progress",
     *     security={{"apiToken": {}}},
     *     tags={"Content Items"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="Content Item ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Content Item data",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ContentItem"
     *              )
     *         )
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
     * @param ContentItem $contentItem
     * @param ContentItemManager $contentItemManager
     * @param ContentItemTransformerInterface $contentItemTransformer
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function reset(ContentItem $contentItem, ContentItemManager $contentItemManager, ContentItemTransformerInterface $contentItemTransformer): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $contentItemManager->resetProgress($contentItem, $user);

        return new JsonResponse(fractal($contentItem, $contentItemTransformer)->toArray());
    }

    /**
     * @SWG\Post(
     *     path="/content-items/{id}/complete",
     *     summary="Complete Content Item and get SIL score",
     *     security={{"apiToken": {}}},
     *     tags={"Content Items"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="Content Item ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Content Item data",
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
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     * )
     *
     * @param ContentItem $contentItem
     * @param ContentItemManager $contentItemManager
     * @param SilScoreService $silScoreService
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function complete(
        ContentItem $contentItem,
        ContentItemManager $contentItemManager,
        SilScoreService $silScoreService
    ): JsonResponse {
        // $this->authorize('complete', $contentItem);
        if ($contentItem->content_type === ContentItemTypeInterface::TIP_CONTENT_TYPE) {
            return new JsonResponse(['error' => 'You can\'t complete Content Item of Tip type']);
        }
        /** @var User $user */
        $user = Auth::user();

        $contentItemManager->complete($user, $contentItem);
        $userSilScore = $user->sil_score;

        $silScoresCollection = new Collection();

        $completeSilScore = SilScore::ofUser($user)->ofContentItem($contentItem)->ofType(SessionCompletedSilScore::getType())->first();

        if ($completeSilScore instanceof SilScore) {
            $userSilScore -= SessionCompletedSilScore::POINTS;
        } else {
            $completeSilScore = $silScoreService->createPayloadedSilScore(new SessionCompletedSilScore($contentItem), $user);
        }

        $silScoresCollection->add($completeSilScore);

        $program = $user->client->activeProgram;

        $userReflection = UserReflection::ofUserAndContentItemAndProgram($user, $contentItem, $program)->orderBy('created_at', 'desc')->first();

        if ($userReflection instanceof UserReflection) {
            $reflectionSilScore = SilScore::ofUserOfReflection($user, $userReflection)->first();

            if ($reflectionSilScore instanceof SilScore) {
                $silScoresCollection->add($reflectionSilScore);
            }
        }

        return new JsonResponse([
            'data' => [
                'sil_score' => $userSilScore,
                'bonus_points' => $silScoresCollection->sum('points')
            ]
        ]);
    }

    /**
     * @SWG\Delete(
     *     path="/content-items/{id}",
     *     summary="Delete content item",
     *     tags={"Content Items"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Content Item id",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Content Item was successfully deleted",
     *             @SWG\Schema(
     *                 type="object",
     *                 @SWG\Property(
     *                     property="message",
     *                     type="string"
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
     * @param ContentItem $contentItem
     * @param ContentItemManager $contentItemManager
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(ContentItem $contentItem, ContentItemManager $contentItemManager): JsonResponse
    {
        $status = $contentItemManager->delete($contentItem);

        return new JsonResponse($status ? ['message' => 'Success operation'] : ['error' => 'Aborted']);
    }

    /**
     * @param ContentItem $contentItem
     * @param ContentItemManager $contentItemManager
     *
     * @param UserFeedbackTransformerInterface $transformer
     *
     * @return JsonResponse
     */
    public function like(ContentItem $contentItem, ContentItemManager $contentItemManager, UserFeedbackTransformerInterface $transformer): JsonResponse
    {
        $feedback = $contentItemManager->likeContentItem($contentItem);

        return new JsonResponse(fractal($feedback, $transformer)->toArray());
    }

    /**
     * @param DislikeRequest $request
     * @param ContentItem $contentItem
     * @param ContentItemManager $contentItemManager
     *
     * @param UserFeedbackTransformerInterface $transformer
     *
     * @return JsonResponse
     */
    public function dislike(DislikeRequest $request, ContentItem $contentItem, ContentItemManager $contentItemManager, UserFeedbackTransformerInterface $transformer): JsonResponse
    {
        $feedback = $contentItemManager->dislikeContentItem($contentItem, $request->getInput());

        return new JsonResponse(fractal($feedback, $transformer)->toArray());
    }
}
