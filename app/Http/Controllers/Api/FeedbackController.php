<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Feedback\CreateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponseInterface;
use App\Manager\FeedbackManager;
use App\Models\Feedback;
use App\Models\User;
use App\Transformers\Feedback\FeedbackTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends ApiController
{
    /**
     *
     * @SWG\Get(
     *     path="/feedback",
     *     summary="Get list of feedback",
     *     tags={"Feedback"},
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
     *     @SWG\Response(
     *         response=200,
     *         description="List of focuses",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="id",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="user_id",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="text",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param FeedbackTransformer $feedbackTransformer
     * @param ListResponseInterface $listResponse
     * @return JsonResponse
     */
    public function index(ListRequest $request, FeedbackTransformer $feedbackTransformer, ListResponseInterface $listResponse): JsonResponse
    {
        return $listResponse
            ->setBuilder(Feedback::query())
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->setSortField('id')
            ->setSortDirection($request->getSortDirection())
            ->setTransformer($feedbackTransformer)
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/feedback",
     *     summary="Create new Feedback",
     *     tags={"Feedback"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Feedback Body",
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="user_id",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="text",
     *                 type="string"
     *             )
     *         )
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
     * @param FeedbackManager $feedbackManager
     *
     * @return JsonResponse
     */
    public function create(CreateRequest $request, FeedbackManager $feedbackManager): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $feedback = $feedbackManager->createFeedback($user, $request->get('text'));

        return new JsonResponse(fractal($feedback, new FeedbackTransformer())->toArray());
    }
}
