<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\TopicArea\AttachContentItemRequest;
use App\Http\Requests\Api\TopicArea\CreateRequest;
use App\Http\Requests\Api\TopicArea\DetachContentItemRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponse;
use App\Http\Responses\ListResponseInterface;
use App\Manager\TopicAreaManager;
use App\Models\ContentItem;
use App\Models\FocusArea;
use App\Models\FocusAreaTopic;
use App\Models\Topic;
use App\Transformers\ContentItem\ContentItemTransformerInterface;
use App\Transformers\TopicArea\TopicAreaTransformerInterface;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Swagger\Annotations as SWG;

class TopicAreaController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/topic-areas",
     *     summary="List of Topic Areas",
     *     security={{"apiToken": {}}},
     *     tags={"Topic Areas"},
     *     @SWG\Parameter(
     *         name="offset",
     *         in="path",
     *         type="integer",
     *         description="Offset"
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         in="path",
     *         type="integer",
     *         description="Limit"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 @SWG\Items(ref="#/definitions/TopicArea")
     *             )
     *         )
     *     ),
     *      @SWG\Response(
     *         response="401",
     *         description="Unauthenticated user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param TopicAreaTransformerInterface $transformer
     * @param ListResponseInterface $response
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ListRequest $request, TopicAreaTransformerInterface $transformer, ListResponseInterface $response): JsonResponse
    {
        $this->authorize('list', FocusAreaTopic::class);

        return $response
            ->setBuilder(FocusAreaTopic::query())
            ->setTransformer($transformer)
            ->setLimit($request->getLimit())
            ->setOffset($request->getOffset())
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/topic-areas",
     *     summary="Add Topic Area",
     *     security={{"apiToken": {}}},
     *     tags={"Topic Areas"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="focus_area_id",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="topic_id",
     *                 type="string",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Created"
     *     ),
     *      @SWG\Response(
     *         response="401",
     *         description="Unauthenticated user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *     ),
     * )
     *
     * @param CreateRequest $request
     * @param TopicAreaManager $topicAreaManager
     * @param TopicAreaTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function create(CreateRequest $request, TopicAreaManager $topicAreaManager, TopicAreaTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('create', FocusAreaTopic::class);

        $focusArea = FocusArea::findOrFail($request->getFocusAreaId());
        $topic = Topic::findOrFail($request->getTopicId());
        $topicArea = $topicAreaManager->create($focusArea, $topic);
        return new JsonResponse(fractal($topicArea, $transformer)->toArray());
    }

    /**
     *
     * @SWG\Delete(
     *     path="/topic-areas/{id}",
     *     summary="Delete Topic Area",
     *     security={{"apiToken": {}}},
     *     tags={"Topic Areas"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="string",
     *         description="Topic Area ID",
     *         in="path",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="No content"
     *     ),
     *      @SWG\Response(
     *         response="401",
     *         description="Unauthenticated user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *     ),
     * )
     *
     * @param FocusAreaTopic $topicArea
     * @param TopicAreaManager $topicAreaManager
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Exception
     */
    public function remove(FocusAreaTopic $topicArea, TopicAreaManager $topicAreaManager): JsonResponse
    {
        $this->authorize('delete', $topicArea);

        $topicAreaManager->delete($topicArea);
        return new JsonResponse(null, 204);
    }

    /**
     * @SWG\Get(
     *     path="/topic-areas/{id}/content-items",
     *     summary="Get list of Content Items of Topic Area",
     *     tags={"Topic Areas"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         description="Topic Area ID",
     *         in="path",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="List of focus areas",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="topic_area",
     *                         ref="#/definitions/TopicArea"
     *                     ),
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/ContentItem")
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
     * @param FocusAreaTopic $topicArea
     * @param TopicAreaTransformerInterface $topicAreaTransformer
     * @param ContentItemTransformerInterface $contentItemTransformer
     * @param ListResponse $response
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function contentItems(
        FocusAreaTopic $topicArea,
        TopicAreaTransformerInterface $topicAreaTransformer,
        ContentItemTransformerInterface $contentItemTransformer,
        ListResponse $response
    ): JsonResponse {
        $this->authorize('show', $topicArea);
        $this->authorize('list', ContentItem::class);

        $builder = $topicArea->contentItems()->getQuery();

        return $response
            ->setBuilder($builder)
            ->setTransformer($contentItemTransformer)
            ->addValue('topic_area', fractal($topicArea, $topicAreaTransformer)->toArray()['data'])
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/topic-areas/{id}/content-items/add",
     *     summary="Add Content Item to Topic Area",
     *     security={{"apiToken": {}}},
     *     tags={"Topic Areas"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="string",
     *         required=true,
     *         description="Topic Area ID",
     *         in="path"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="content_item_id",
     *                 type="string",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Created"
     *     ),
     *      @SWG\Response(
     *         response="401",
     *         description="Unauthenticated user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *     ),
     * )
     *
     * @param AttachContentItemRequest $request
     * @param FocusAreaTopic $topicArea
     * @param TopicAreaManager $topicAreaManager
     *
     * @return JsonResponse
     */
    public function attachContentItem(AttachContentItemRequest $request, FocusAreaTopic $topicArea, TopicAreaManager $topicAreaManager): JsonResponse
    {
        $contentItem = ContentItem::findOrFail($request->getContentItemId());
        $topicAreaManager->attachContentItem($topicArea, $contentItem);

        return new JsonResponse(null, 204);
    }

    /**
     * @SWG\Post(
     *     path="/topic-areas/{id}/content-items/remove",
     *     summary="Add Content Item to Topic Area",
     *     security={{"apiToken": {}}},
     *     tags={"Topic Areas"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="string",
     *         required=true,
     *         description="Topic Area ID",
     *         in="path"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="content_item_id",
     *                 type="string",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Created"
     *     ),
     *      @SWG\Response(
     *         response="401",
     *         description="Unauthenticated user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *     ),
     * )
     *
     * @param DetachContentItemRequest $request
     * @param FocusAreaTopic $topicArea
     * @param TopicAreaManager $topicAreaManager
     *
     * @return JsonResponse
     */
    public function detachContentItem(DetachContentItemRequest $request, FocusAreaTopic $topicArea, TopicAreaManager $topicAreaManager): JsonResponse
    {
        $contentItem = ContentItem::findOrFail($request->getContentItemId());
        $topicAreaManager->detachContentItem($topicArea, $contentItem);

        return new JsonResponse(null, 204);
    }
}
