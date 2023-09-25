<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Program\AddFocusRequest;
use App\Http\Requests\Api\Program\UpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponseInterface;
use App\Manager\ProgramManager;
use App\Http\Requests\Api\Program\CreateRequest;
use App\Models\Client;
use App\Models\Focus;
use App\Models\FocusArea;
use App\Models\Program;
use App\Models\User;
use App\Transformers\FocusArea\FocusAreaTransformerInterface;
use App\Transformers\Program\ProgramTransformerInterface;
use Auth;
use Illuminate\Http\JsonResponse;
use Swagger\Annotations as SWG;

class ProgramController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/programs",
     *     summary="Get Programs list",
     *     tags={"Programs"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="client_id",
     *         type="integer",
     *         in="query",
     *         description="Filter by Clinet ID"
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         type="integer",
     *         in="query"
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         type="integer",
     *         in="query"
     *     ),
     *     @SWG\Parameter(
     *         name="sort",
     *         type="string",
     *         in="query",
     *         description="asc, desc"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success operation",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/Program")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthenticated user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param ProgramTransformerInterface $transformer
     *
     * @param ListResponseInterface $response
     *
     * @return JsonResponse
     */
    public function index(ListRequest $request, ProgramTransformerInterface $transformer, ListResponseInterface $response): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $builder = Program::ofUser($user);

        if ($request->get('client_id')) {
            $client = Client::findOrFail($request->get('client_id'));
            $response->setWhere(['client_id' => $client->id]);
        }

        $response
            ->setBuilder($builder)
            ->setTransformer($transformer)
            ->setLimit($request->getLimit())
            ->setOffset($request->getOffset())
            ->setSortField('created_at')
            ->setSortDirection('asc');

        return $response->getResponse();
    }

    /**
     *
     * @SWG\Put(
     *     path="/programs/{id}",
     *     summary="Update Program ",
     *     tags={"Programs"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Program Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         description="Id of Program",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="name",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="status",
     *                 type="boolean"
     *             ),
     *             @SWG\Property(
     *                 property="client_id",
     *                 type="integer"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Program data",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Program"
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
     * @param UpdateRequest $request
     * @param Program $program
     * @param ProgramManager $programManager
     * @param ProgramTransformerInterface $transformer
     * @return JsonResponse
     */
    public function update(
        UpdateRequest $request,
        Program $program,
        ProgramManager $programManager,
        ProgramTransformerInterface $transformer
    ): JsonResponse {
        $programManager->update($program, $request->validated());

        return new JsonResponse(fractal($program, $transformer)->toArray());
    }

    /**
     *
     * @SWG\Get(
     *     path="/programs/{id}/focus-areas",
     *     summary="Get list of Focus Areas",
     *     tags={"Programs"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         description="Program ID",
     *         in="path",
     *         type="string",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         description="limit",
     *         in="query",
     *         type="integer",
     *         required=false
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         description="offset",
     *         in="query",
     *         type="integer",
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
     *                 property="program",
     *                 ref="#/definitions/Program",
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(ref="#/definitions/FocusArea"),
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
     * @param Program $program
     * @param ProgramTransformerInterface $programTransformer
     * @param FocusAreaTransformerInterface $focusAreaTransformer
     * @param ListResponseInterface $listResponse
     *
     * @return JsonResponse
     */
    public function focusAreas(
        ListRequest $request,
        Program $program,
        ProgramTransformerInterface $programTransformer,
        FocusAreaTransformerInterface $focusAreaTransformer,
        ListResponseInterface $listResponse
    ): JsonResponse {
        $builder = FocusArea::ofProgram($program);

        return $listResponse
            ->setBuilder($builder)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->setTransformer($focusAreaTransformer)
            ->addValue('program', fractal($program, $programTransformer)->toArray()['data'])
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/programs",
     *     summary="Create Program",
     *     security={{"apiToken": {}}},
     *     tags={"Programs"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             ref="#/definitions/ProgramBody"
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success operation",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/Program")
     *                     )
     *                 )
     *             }
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
     * @param CreateRequest $request
     * @param ProgramTransformerInterface $transformer
     * @param ProgramManager $programManager
     * @return JsonResponse
     */
    public function create(CreateRequest $request, ProgramTransformerInterface $transformer, ProgramManager $programManager): JsonResponse
    {
        $client = Client::findOrFail($request->getClientId());
        $program = $programManager->createProgram($client, $request->getName(), $request->getStatus() ?? Program::STATUS_DRAFT);
        return new JsonResponse(fractal($program, $transformer)->toArray());
    }

    /**
     * @SWG\Post(
     *     path="/programs/{id}/focuses/add",
     *     summary="Add Focus to Program",
     *     security={{"apiToken": {}}},
     *     tags={"Programs"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="string",
     *         description="Program ID",
     *         in="path",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="focus_id",
     *                 type="integer",
     *             )
     *         )
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
     * @param AddFocusRequest $request
     * @param Program $program
     * @param ProgramManager $programManager
     *
     * @return JsonResponse
     */
    public function addFocus(AddFocusRequest $request, Program $program, ProgramManager $programManager): JsonResponse
    {
        $focus = Focus::findOrFail($request->getFocusId());
        $programManager->addFocus($program, $focus);
        return new JsonResponse(null, 204);
    }

    /**
     * @SWG\Post(
     *     path="/programs/{id}/focuses/remove",
     *     summary="Remove Focus from Program",
     *     security={{"apiToken": {}}},
     *     tags={"Programs"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="string",
     *         description="Program ID",
     *         in="path",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="focus_id",
     *                 type="integer",
     *             )
     *         )
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
     * @param AddFocusRequest $request
     * @param Program $program
     * @param ProgramManager $programManager
     *
     * @return JsonResponse
     */
    public function removeFocus(AddFocusRequest $request, Program $program, ProgramManager $programManager): JsonResponse
    {
        $focus = Focus::findOrFail($request->getFocusId());
        $programManager->removeFocus($program, $focus);
        return new JsonResponse(null, 204);
    }
}
