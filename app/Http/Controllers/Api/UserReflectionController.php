<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ActivityNotFoundException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\UserReflection\CreateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\JsonErrorResponse;
use App\Http\Responses\JsonTransformedResponse;
use App\Http\Responses\ListResponseInterface;
use App\Manager\UserReflectionManager;
use App\Models\ContentItem;
use App\Models\ContentItemUserProgress;
use App\Models\User;
use App\Models\UserReflection;
use App\Transformers\UserReflection\UserReflectionTransformerInterface;
use Auth;
use Illuminate\Http\JsonResponse;
use Swagger\Annotations as SWG;

class UserReflectionController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/reflections",
     *     summary="Get list of reflections",
     *     tags={"Reflections"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="sort",
     *         description="Sort direction",
     *         in="query",
     *         type="string",
     *         required=false
     *     ),
     *     @SWG\Parameter(
     *         name="user_id",
     *         description="Filter by User ID",
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
     *                         @SWG\Items(ref="#/definitions/Reflection")
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
     * @param ListRequest $request
     * @param UserReflectionTransformerInterface $userReflectionTransformer
     * @param ListResponseInterface $listResponse
     * @return JsonResponse
     */
    public function index(ListRequest $request, UserReflectionTransformerInterface $userReflectionTransformer, ListResponseInterface $listResponse): JsonResponse
    {
        return $listResponse
            ->setBuilder(UserReflection::query())
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->setSortField('user_id')
            ->setSortDirection($request->getSortDirection())
            ->setTransformer($userReflectionTransformer)
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/reflections",
     *     summary="Create new Reflection",
     *     tags={"Reflections"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Reflection Body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/ReflectionBody"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(
     *                     ref="#/definitions/Reflection"
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
     * @param CreateRequest $request
     * @param UserReflectionManager $userReflectionManager
     *
     * @param UserReflectionTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws ActivityNotFoundException
     */
    public function create(
        CreateRequest $request,
        UserReflectionManager $userReflectionManager,
        UserReflectionTransformerInterface $transformer
    ): JsonResponse {
        $contentItem = ContentItem::findOrFail($request->getContentItemId());
        $user = Auth::user();

        if ($request->get('user_id')) {
            $user = User::findOrFail($request->get('user_id'));
        }

        $skipped = $request->getSkipped() || $request->getInput() === '' || $request->getInput() === null;

        $program = $user->client->activeProgram;

        $userProgress = ContentItemUserProgress::ofUserAndItemAndProgram($user, $contentItem, $program)->first();

        if (!$userProgress instanceof ContentItemUserProgress) {
            return new JsonErrorResponse('You can not write reflection to non started session');
        }

        $userReflection = $userReflectionManager->createUserReflection($user, $contentItem, $program, $userProgress, $skipped, $request->getInput() ?? '');

        return new JsonTransformedResponse($userReflection, $transformer, 201);
    }
}
