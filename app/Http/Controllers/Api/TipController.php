<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Manager\TipManager;
use App\Models\ContentItem;
use App\Models\User;
use App\Transformers\Tip\TipTransformer;
use Auth;
use Illuminate\Http\JsonResponse;

class TipController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/tips/{id}",
     *     summary="Get Tip",
     *     tags={"Tips"},
     *     operationId="getTip",
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Tip id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 @SWG\Property(
     *                     property="focus_id",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="topic_id",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="tip",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="accent_color",
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
     * @param ContentItem $contentItem
     * @param TipTransformer $transformer
     *
     * @return JsonResponse
     */
    public function show(ContentItem $contentItem, TipTransformer $transformer, TipManager $tipManager): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $tipManager->setViewed($contentItem, $user);

        return new JsonResponse(fractal($contentItem, $transformer)->toArray());
    }
}
