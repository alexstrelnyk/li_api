<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\FocusArea\CreateRequest;
use App\Http\Requests\Api\FocusArea\UpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponse;
use App\Http\Responses\ListResponseInterface;
use App\Manager\FocusAreaManager;
use App\Models\Focus;
use App\Models\FocusArea;
use App\Models\FocusAreaTopic;
use App\Models\Program;
use App\Transformers\FocusArea\FocusAreaTransformerInterface;
use App\Transformers\TopicArea\TopicAreaTransformerInterface;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Swagger\Annotations as SWG;

class FocusAreaController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/focus-areas",
     *     summary="Get list of Focus Areas",
     *     tags={"Focus Areas"},
     *     security={{"apiToken": {}}},
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
     *     @SWG\Response(
     *         response=200,
     *         description="List of focus areas",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/FocusArea")
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
     * @param FocusAreaTransformerInterface $focusAreaTransformer
     * @param ListResponseInterface $listResponse
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ListRequest $request, FocusAreaTransformerInterface $focusAreaTransformer, ListResponseInterface $listResponse): JsonResponse
    {
        $this->authorize('list', FocusArea::class);

        return $listResponse
            ->setBuilder(FocusArea::query())
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->setTransformer($focusAreaTransformer)
            ->getResponse();
    }

    /**
     *
     * @SWG\Post(
     *     path="/focus-areas",
     *     summary="Create Focus Area",
     *     tags={"Focus Areas"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Body of Focus Area",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="program_id",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="focus_id",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="status",
     *                 type="integer",
     *                 description="available statuses: 1 - Draft | 2 - Review | 3 - Published",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Focus Area data"
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
     * @param CreateRequest $createRequest
     * @param FocusAreaManager $focusAreaManager
     * @param FocusAreaTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function create(CreateRequest $createRequest, FocusAreaManager $focusAreaManager, FocusAreaTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('create', FocusArea::class);
        $validData = $createRequest->validated();

        $program = Program::findOrFail($validData['program_id']);

        $check = DB::table('focus_area')->where('program_id', $validData['program_id'])->where('focus_id', $validData['focus_id'])->first();

        if ($check) {
            return new JsonResponse([
                'focus_id' => ['This focus id is already used with this program']
            ], 422);
        }

        $focus = Focus::findOrFail($validData['focus_id']);

        $focusArea = $focusAreaManager->create($program, $focus, $validData);

        return new JsonResponse(fractal($focusArea, $transformer)->toArray());
    }

    /**
     * @SWG\Get(
     *     path="/focus-areas/{id}",
     *     summary="Show Focus Area data",
     *     tags={"Focus Areas"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         description="Focus Area ID",
     *         in="path",
     *         type="string",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Focus Area data"
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
     * @param FocusArea $focusArea
     * @param FocusAreaTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(FocusArea $focusArea, FocusAreaTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('show', $focusArea);

        return new JsonResponse(fractal($focusArea, $transformer)->toArray());
    }

    /**
     * @SWG\Put(
     *     path="/focus-areas/{id}",
     *     summary="Update Focus Area data",
     *     tags={"Focus Areas"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         description="Focus Area ID",
     *         in="path",
     *         type="string",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         required=true,
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="integer"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Focus Area data"
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
     * @param UpdateRequest $request
     * @param FocusArea $focusArea
     * @param FocusAreaManager $focusAreaManager
     * @param FocusAreaTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, FocusArea $focusArea, FocusAreaManager $focusAreaManager, FocusAreaTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('edit', $focusArea);
        $focusAreaManager->update($focusArea, $request->validated());

        return new JsonResponse(fractal($focusArea, $transformer)->toArray());
    }

    /**
     * @SWG\Delete(
     *     path="/focus-areas/{id}",
     *     summary="Delete Focus Area",
     *     security={{"apiToken": {}}},
     *     tags={"Focus Areas"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="string",
     *         description="Focus Area ID",
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
     * @param FocusArea $focusArea
     * @param FocusAreaManager $focusAreaManager
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function remove(FocusArea $focusArea, FocusAreaManager $focusAreaManager): JsonResponse
    {
        $this->authorize('delete', $focusArea);
        $focusAreaManager->delete($focusArea);

        return new JsonResponse(null, 204);
    }

    /**
     * @SWG\Get(
     *     path="/focus-areas/{id}/topic-areas",
     *     summary="Get list of Topic Area of Focus Area",
     *     tags={"Focus Areas"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         required=true,
     *         description="Focus Area ID",
     *         in="path"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="List of focus areas",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/TopicArea")
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
     * @param FocusArea $focusArea
     * @param ListResponse $response
     * @param FocusAreaTransformerInterface $focusAreaTransformer
     * @param TopicAreaTransformerInterface $topicAreaTransformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function topicAreas(
        FocusArea $focusArea,
        ListResponse $response,
        FocusAreaTransformerInterface $focusAreaTransformer,
        TopicAreaTransformerInterface $topicAreaTransformer
    ): JsonResponse {
        $this->authorize('show', $focusArea);
        $this->authorize('list', FocusAreaTopic::class);

        $topicAreasBuilder = $focusArea->topicAreas()->getQuery();

        return $response
            ->setBuilder($topicAreasBuilder)
            ->setTransformer($topicAreaTransformer)
            ->addValue('focus_area', fractal($focusArea, $focusAreaTransformer)->toArray()['data'])
            ->getResponse();
    }
}
